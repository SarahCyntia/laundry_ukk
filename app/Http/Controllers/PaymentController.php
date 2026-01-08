<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class PaymentController extends Controller
{
    public function getSnapToken($id)
    {
        $order = Order::with('pelanggan')->findOrFail($id);

        if ($order->status_pembayaran === 'pending' && $order->snap_token) {
            return response()->json([
                'snap_token' => $order->snap_token
            ]);
        }

        // === KONFIG MIDTRANS (BENAR) ===
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // $grossAmount = (float) $order->harga_final;

        $grossAmount = (int) round($order->harga_final);
if ($grossAmount < 1) {
    return response()->json([
        'message' => 'Biaya order harus lebih dari 0'
    ], 422);
}

$payload = [
    'transaction_details' => [
        'order_id' => $order->kode_order,
        'gross_amount' => $grossAmount,
    ],
    'customer_details' => [
        'first_name' => $order->pelanggan->name ?? 'Customer',
        'email' => $order->pelanggan->email ?? 'user@gmail.com',
    ],
];

        // $payload = [
        //     'transaction_details' => [
        //         'order_id' => $order->kode_order,
        //         'gross_amount' => (int) $order->biaya,
        //     ],
        //     'customer_details' => [
        //         'first_name' => $order->pelanggan->nama ?? 'Customer',
        //         'email' => $order->pelanggan->email ?? 'user@gmail.com',
        //     ],
        // ];

        $snapToken = Snap::getSnapToken($payload);

        $order->update([
            'snap_token' => $snapToken,
            'status_pembayaran' => 'pending'
        ]);

        return response()->json([
            'snap_token' => $snapToken
        ]);
    }

    // === WEBHOOK MIDTRANS ===
    public function handleNotification(Request $request)
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $notification = new Notification();

        $order = Order::where('kode_order', $notification->order_id)->first();

        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        switch ($notification->transaction_status) {
            case 'settlement':
                $order->status_pembayaran = 'settlement';
                break;

            case 'pending':
                $order->status_pembayaran = 'pending';
                break;

            case 'deny':
            case 'cancel':
            case 'expire':
                $order->status_pembayaran = 'failure';
                break;
        }

        $order->save();

        return response()->json(['message' => 'OK']);
    }

    public function manualUpdateStatus(Request $request)
    {
        $order = Order::where('kode_order', $request->order_id)->first();

        if (!$order) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        $order->update([
            'status_pembayaran' => $request->transaction_status ?? 'settlement'
        ]);

        Log::info("Manual update order {$order->kode_order}");

        return response()->json(['message' => 'Status pembayaran diperbarui']);
    }
}
