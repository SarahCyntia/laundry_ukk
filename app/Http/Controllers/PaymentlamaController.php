<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Notification;

class PaymentlamaController extends Controller
{
    public function __construct()
    {
        // konfigurasi midtrans dari env
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Buat transaksi & ambil snap token
     * POST /api/payment/create
     * body: { order_id: int }
     */
    public function create(Request $request)
    {
        $request->validate(['order_id' => 'required|integer']);

        $order = Order::find($request->order_id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order tidak ditemukan'], 404);
        }

        // optional: cek apakah order sudah siap untuk dibayar (sesuaikan logika)
        // contoh: if ($order->status !== 'siap_dibayar') { ... }

        // hitung total harga; sesuaikan field di Order mu
        $total = (int) ($order->harga_final ?? $order->total_harga ?? 0);
        if ($total <= 0) {
            return response()->json(['success' => false, 'message' => 'Total harga tidak valid'], 400);
        }

        // generate midtrans order id unik
        $midtransOrderId = 'INV-' . time() . '-' . $order->id;

        // buat record transaksi
        $transaksi = Transaksi::create([
            'order_id' => $order->id,
            'user_id'  => $order->pelanggan_id ?? auth()->id(),
            'mitra_id' => $order->mitra_id,
            'berat'    => $order->berat_aktual ?? $order->berat_estimasi,
            'total_harga' => $total,
            'midtrans_order_id' => $midtransOrderId,
            'transaction_status' => 'pending',
        ]);

        // prepare params midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => optional($order->pelanggan)->name ?? optional($order->user)->name ?? 'Customer',
                'email' => optional($order->pelanggan)->email ?? optional($order->user)->email ?? null,
                'phone' => optional($order->pelanggan)->phone ?? null,
            ],
            // kamu bisa batasi payment method di sini
            //'enable_payments' => ['gopay','bank_transfer','qris','credit_card'],
        ];

        try {
            $snap = Snap::createTransaction($params);
            // $snap adalah object yang berisi token dan redirect url pada beberapa versi
            $snapToken = $snap->token ?? null;
            $redirectUrl = $snap->redirect_url ?? null;

            $transaksi->update([
                'snap_token' => $snapToken,
            ]);

            return response()->json([
                'success' => true,
                'transaksi_id' => $transaksi->id,
                'snap_token' => $snapToken,
                'redirect_url' => $redirectUrl,
                'midtrans_order_id' => $midtransOrderId,
            ]);
        } catch (\Exception $e) {
            // hapus transaksi jika gagal bikin token
            try { $transaksi->delete(); } catch (\Throwable $t) {}
            Log::error('Midtrans create error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal membuat transaksi Midtrans', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Ambil snap token berdasar transaksi id (jika ingin implementasi 2 langkah)
     * GET /api/payment/token/{id}
     */
    public function token($id)
    {
        $trans = Transaksi::find($id);
        if (!$trans) return response()->json(['snap_token' => null], 404);

        return response()->json(['snap_token' => $trans->snap_token]);
    }

    /**
     * Manual update status (dipanggil dari frontend setelah onSuccess/onPending)
     * POST /api/manual-update-status
     * body: { order_id: midtrans_order_id, transaction_status, payment_type }
     */
    public function manualUpdateStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string', // midtrans order id
            'transaction_status' => 'required|string'
        ]);

        $midtransOrderId = $request->order_id;
        $trans = Transaksi::where('midtrans_order_id', $midtransOrderId)->first();
        if (!$trans) return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);

        $trans->transaction_status = $request->transaction_status;
        $trans->payment_type = $request->payment_type ?? $trans->payment_type;
        $trans->save();

        // update order jika settlement
        if ($request->transaction_status === 'settlement') {
            $order = $trans->order;
            if ($order) {
                $order->status = 'dibayar'; // atau status lain sesuai flow
                $order->save();
            }
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Callback / webhook Midtrans
     * POST /api/midtrans/callback
     * PUBLIC endpoint (Midtrans akan POST ke sini)
     */
    public function callback(Request $request)
    {
        try {
            $notif = new Notification();

            $transactionStatus = $notif->transaction_status ?? $request->transaction_status ?? null;
            $orderId = $notif->order_id ?? $request->order_id ?? null; // ini midtrans order id
            $paymentType = $notif->payment_type ?? $request->payment_type ?? null;

            Log::info("Midtrans callback received: order_id={$orderId}, status={$transactionStatus}");

            if (!$orderId) {
                return response()->json(['message' => 'order_id tidak ditemukan di payload'], 400);
            }

            $transaksi = Transaksi::where('midtrans_order_id', $orderId)->first();
            if (!$transaksi) {
                Log::warning("Transaksi not found for midtrans_order_id: {$orderId}");
                return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
            }

            // Update transaksi fields
            $transaksi->transaction_status = $transactionStatus;
            $transaksi->payment_type = $paymentType ?? $transaksi->payment_type;

            // jika va_numbers ada, ambil payment_code
            if (isset($notif->va_numbers) && is_array($notif->va_numbers) && count($notif->va_numbers) > 0) {
                $transaksi->payment_code = $notif->va_numbers[0]->va_number ?? $transaksi->payment_code;
            } elseif (isset($notif->payment_code)) {
                $transaksi->payment_code = $notif->payment_code;
            }

            $transaksi->save();

            // update order berdasarkan status
            $order = $transaksi->order;
            if ($order) {
                if ($transactionStatus === 'settlement') {
                    $order->status = 'dibayar';
                    $order->save();
                } elseif ($transactionStatus === 'expire') {
                    $order->status = 'expired_payment';
                    $order->save();
                } elseif (in_array($transactionStatus, ['cancel','deny'])) {
                    $order->status = 'payment_failed';
                    $order->save();
                }
            }

            return response()->json(['message' => 'OK']);
        } catch (\Exception $e) {
            Log::error("Midtrans callback error: " . $e->getMessage());
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], 500);
        }
    }
}
