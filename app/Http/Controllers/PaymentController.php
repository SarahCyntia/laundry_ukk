<?php

namespace App\Http\Controllers;

// use App\Models\order;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Midtrans\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;
use Midtrans\Transaction;

class PaymentController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:order,id',
            'customer_first_name' => 'required|string|max:100',
            'customer_email' => 'required|email',
            'item_name' => 'required|string|max:255',
        ]);

        $order = Order::findOrFail($request->id);
        $price = $order->harga_final;

        if (!$price || $price <= 0) {
            return response()->json(['error' => 'Harga dari order tidak valid.'], 400);
        }

        $orderId = Str::uuid();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $price
            ],
            'item_details' => [
                [
                    'price' => $price,
                    'quantity' => 1,
                    'name' => $request->item_name,
                ]
            ],
            'customer_details' => [
                'first_name' => $request->customer_first_name,
                'email' => $request->customer_email,
            ],
            'enabled_payment' => ['credit_card', 'bca_va', 'bni_va', 'bri_va']
        ];

        $auth = base64_encode(env('MIDTRANS_SERVER_KEY') . ':');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Basic $auth",
        ])->post('https://app.sandbox.midtrans.com/snap/v1/transactions', $params);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Gagal memproses ke Midtrans',
                'midtrans_response' => json_decode($response->body(), true)
            ], 500);
        }

        $data = json_decode($response->body());

        $payment = new Payment();
        $payment->order_id = $orderId;
        $payment->status = 'pending';
        $payment->price = $price;
        $payment->customer_first_name = $request->customer_first_name;
        $payment->customer_email = $request->customer_email;
        $payment->item_name = $request->item_name;
        $payment->checkout_link = $data->redirect_url ?? null;
        $payment->save();

        return response()->json($data);
    }

    public function webhook(Request $request)
    {
        $auth = base64_encode(env('MIDTRANS_SERVER_KEY') . ':');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Basic $auth",
        ])->get("https://api.sandbox.midtrans.com/v2/$request->order_id/status");

        $response = json_decode($response->body());

        $payment = Payment::where('order_id', $response->order_id)->firstOrFail();

        if (in_array($payment->status, ['settlement', 'capture'])) {
            return response()->json('Payment has been already processed');
        }

        $payment->status = $response->transaction_status;
        $payment->save();

        return response()->json('success');
    }
    // public function store(Request $request)
    // {
    //     $transaksii = $request->validate([
    //         'penerima' => 'required|string',
    //         'pengirim' => 'required|string',
    //         'no_hp_penerima' => 'required|string',
    //         'tujuan_provinsi_id' => 'required|exists:provinces,id',
    //         'tujuan_kota_id' => 'required|exists:cities,id',
    //         'alamat_tujuan' => 'required|string',
    //         'nama_barang' => 'required|string',
    //         'berat_barang' => 'required|numeric|min:0.01',
    //         'ekspedisi' => 'required|string',
    //         'layanan' => 'required|string',
    //         'biaya' => 'required|integer',
    //         'asal_provinsi_id' => 'required|exists:provinces,id',
    //         'asal_kota_id' => 'required|exists:cities,id',
    //         'alamat_asal' => 'required|string',
    //         'waktu' => 'nullable|date|before_or_equal:now',
    //         'rating' => 'nullable|integer|min:1|max:5',
    //         'status' => 'nullable|string',
    //         'komentar' => 'nullable|string',
    //         'kurir_id' => 'nullable|exists:kurir,kurir_id'
    //     ]);

    //     $noResi = 'ABC-' . strtoupper(uniqid());

    //     // Buat payload pembayaran ke Midtrans
    //     $payload = [
    //         'transaction_details' => [
    //             'order_id' => $noResi,
    //             'gross_amount' => $request->biaya,
    //         ],
    //         'customer_details' => [
    //             'first_name' => $request->pengirim,
    //             'phone' => $request->no_hp_penerima,
    //         ],
    //         'enabled_payment' => ['gopay', 'bank_transfer'],
    //     ];

    //     // Kirim request ke Midtrans
    //     $midtransResponse = Http::withBasicAuth(env('MIDTRANS_SERVER_KEY'), '')
    //         ->post('https://api.sandbox.midtrans.com/v2/charge', array_merge($payload, ['payment_type' => 'bank_transfer', 'bank_transfer' => ['bank' => 'bca']]));

    //     if (!$midtransResponse->ok()) {
    //         return response()->json(['message' => 'Gagal membuat transaksi di Midtrans'], 500);
    //     }

    //     $midtransData = $midtransResponse->json();

    //     // Tentukan status pembayaran berdasarkan response Midtrans
    //     $statusPembayaran = $midtransData['transaction_status'] ?? 'pending';

    //     // Simpan transaksi ke database
    //     $order = Order::create([
    //         'no_resi' => $noResi,
    //         'nama_barang' => $request->nama_barang,
    //         'berat_barang' => $request->berat_barang,
    //         'alamat_asal' => $request->alamat_asal,
    //         'alamat_tujuan' => $request->alamat_tujuan,
    //         'penerima' => $request->penerima,
    //         'pengirim' => $request->pengirim,
    //         'no_hp_penerima' => $request->no_hp_penerima,
    //         'status' => $request->status,
    //         'ekspedisi' => $request->ekspedisi,
    //         'layanan' => $request->layanan,
    //         'biaya' => $request->biaya,
    //         'rating' => $request->rating,
    //         'komentar' => $request->komentar,
    //         'waktu' => now(),
    //         'asal_provinsi_id' => $request->asal_provinsi_id,
    //         'asal_kota_id' => $request->asal_kota_id,
    //         'tujuan_provinsi_id' => $request->tujuan_provinsi_id,
    //         'tujuan_kota_id' => $request->tujuan_kota_id,
    //         'status_pembayaran' => $statusPembayaran,
    //     ]);

    //     return response()->json([
    //         'message' => 'order berhasil dibuat',
    //         'data' => $order,
    //         'midtrans' => $midtransData // Kirim data Midtrans ke frontend jika perlu
    //     ]);
    // }
    
    public function getSnapToken($id)
{
    $order = Order::with('mitra')->findOrFail($id);

    if ($order->harga_final <= 0) {
        return response()->json(['message' => 'Harga tidak valid'], 400);
    }

    // Midtrans config
    Config::$serverKey = config('services.midtrans.server_key');
    Config::$isProduction = false;
    Config::$isSanitized = true;
    Config::$is3ds = true;

    $orderIdMidtrans = $order->kode_order;

    // 🔎 Cari transaksi dulu
    $transaksi = Transaksi::where('order_id', $order->id)->first();

    if ($transaksi && $transaksi->status_pembayaran === 'menunggu_pembayaran' && $transaksi->snap_token) {
        return response()->json([
            'snap_token' => $transaksi->snap_token
        ]);
    }

    // ✅ Kalau snap_token sudah ada & belum dibayar → pakai ulang
    if ($transaksi && $transaksi->snap_token && $transaksi->status_pembayaran !== 'dibayar') {
        return response()->json([
            'snap_token' => $transaksi->snap_token
        ]);
    }

    $params = [
        'transaction_details' => [
            'order_id' => $orderIdMidtrans,
            'gross_amount' => (int) $order->harga_final,
        ],
        'customer_details' => [
            'first_name' => $order->mitra->nama_laundry ?? 'User',
            'email' => $order->mitra->email ?? 'user@email.com',
        ],
        'expiry' => [
            'start_time' => now()->format('Y-m-d H:i:s O'),
            'unit' => 'minute',
            'duration' => 30
        ],
    ];

    $snapToken = Snap::getSnapToken($params);

    // ✅ SIMPAN / UPDATE TRANSAKSI
    if ($transaksi) {
        // Jangan reset kalau sudah dibayar
        if ($transaksi->status_pembayaran !== 'dibayar') {
            $transaksi->update([
                'snap_token' => $snapToken,
                'payment_reference' => $orderIdMidtrans,
                'total_bayar' => $order->harga_final,
                'status_pembayaran' => 'menunggu_pembayaran',
            ]);
        }
    } else {
        Transaksi::create([
            'order_id' => $order->id,
            'payment_reference' => $orderIdMidtrans,
            'total_bayar' => $order->harga_final,
            'snap_token' => $snapToken,
            'status_pembayaran' => 'menunggu_pembayaran',
        ]);
    }

    return response()->json([
        'snap_token' => $snapToken
    ]);
}

// public function getSnapToken($id)
//     {
//         $order = Order::with('mitra')->findOrFail($id);

//         if ($order->harga_final <= 0) {
//             return response()->json(['message' => 'Harga tidak valid'], 400);
//         }

//         // Midtrans config
//         Config::$serverKey = config('services.midtrans.server_key');
//         Config::$isProduction = false; // true kalau production
//         Config::$isSanitized = true;
//         Config::$is3ds = true;

//         // ✅ ORDER ID STABIL (WAJIB)
//         $orderIdMidtrans = $order->kode_order;

//         $params = [
//             'transaction_details' => [
//                 'order_id' => $orderIdMidtrans,
//                 'gross_amount' => (int) $order->harga_final,
//             ],
//             'customer_details' => [
//                 'first_name' => $order->mitra->nama_laundry ?? 'User',
//                 'email' => $order->mitra->email ?? 'user@email.com',
//             ],
//         ];

//         $snapToken = Snap::getSnapToken($params);

//         // ✅ SIMPAN / UPDATE TRANSAKSI
//         Transaksi::updateOrCreate(
//             ['order_id' => $order->id],
//             [
//                 'payment_reference' => $orderIdMidtrans,
//                 'total_bayar' => $order->harga_final,
//                 'status_pembayaran' => 'menunggu_pembayaran',
//             ]
//         );

//         return response()->json([
//             'snap_token' => $snapToken
//         ]);
//     }

    public function midtransCallback(Request $request)
    {
        Log::info('CALLBACK MASUK', $request->all());
        $serverKey = config('services.midtrans.server_key');

        // 🔐 Validasi signature
        $signature = hash(
            'sha512',
            $request->order_id .
            $request->status_code .
            $request->gross_amount .
            $serverKey
        );

        if ($signature !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        Log::info('MIDTRANS CALLBACK', $request->all());

        $transaksi = Transaksi::where(
            'payment_reference',
            $request->order_id
        )->first();

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        // ✅ MAP STATUS MIDTRANS → ENUM DB
        $statusMap = [
            'pending'    => 'menunggu_pembayaran',
            'settlement' => 'dibayar',
            'expire'     => 'kadaluarsa',
            'cancel'     => 'dibatalkan',
            'refund'     => 'dikembalikan',
        ];

        $status = $statusMap[$request->transaction_status]
            ?? 'menunggu_pembayaran';

        // ✅ UPDATE TRANSAKSI
        $transaksi->update([
            'status_pembayaran' => $status,
            'metode_pembayaran' => $request->payment_type,
            'waktu_bayar' => $status === 'dibayar' ? now() : null,
        ]);

        // ✅ UPDATE ORDER (AMAN)
        if ($transaksi->order) {
            $transaksi->order->update([
                'status_pembayaran' => $status,
            ]);
        }

        return response()->json(['message' => 'Callback OK']);
    }
    
// public function getSnapToken($id)
// {
//     $order = Order::with('mitra')->findOrFail($id);

//     if ($order->harga_final <= 0) {
//         return response()->json(['message' => 'Harga tidak valid'], 400);
//     }

//     Config::$serverKey = config('services.midtrans.server_key');
//     Config::$isProduction = false;
//     Config::$isSanitized = true;
//     Config::$is3ds = true;

//     // 🔥 ORDER ID UNIK & KONSISTEN
//     // $orderIdMidtrans = $order->kode_order . '-' . now()->timestamp;
//     $orderIdMidtrans = $order->kode_order;


//     $params = [
//         'transaction_details' => [
//             'order_id' => $orderIdMidtrans,
//             'gross_amount' => (int) $order->harga_final,
//         ],
//         'customer_details' => [
//             'first_name' => $order->mitra->nama_laundry ?? 'User',
//             'email' => $order->mitra->email ?? 'user@email.com',
//         ],
//     ];

//     $snapToken = Snap::getSnapToken($params);

//     // 🔥 SIMPAN TRANSAKSI
//     Transaksi::updateOrCreate(
//         ['order_id' => $order->id],
//         [
//             'total_bayar' => $order->harga_final,
//             'status_pembayaran' => 'belum_dibayar',
//             'payment_reference' => $orderIdMidtrans,
//         ]
//     );

//     return response()->json(['snap_token' => $snapToken]);
// }

// public function midtransCallback(Request $request)
// {
//     $serverKey = config('services.midtrans.server_key');

//     // 🔐 VALIDASI SIGNATURE
//     $signature = hash(
//         'sha512',
//         $request->order_id .
//         $request->status_code .
//         $request->gross_amount .
//         $serverKey
//     );

//     if ($signature !== $request->signature_key) {
//         return response()->json(['message' => 'Invalid signature'], 403);
//     }

//     $transaksi = Transaksi::where('payment_reference', $request->order_id)->first();

//     if (!$transaksi) {
//         return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
//     }

//     // 🔥 MAP STATUS MIDTRANS → ENUM DB
//     $statusMap = [
//         // 'pending'    => 'menunggu_pembayaran',
//         'settlement' => 'dibayar',
//         'expire'     => 'kadaluarsa',
//         'cancel'     => 'dibatalkan',
//         'refund'     => 'dikembalikan',
//     ];

//     $status = $statusMap[$request->transaction_status] ?? 'belum_dibayar';

//     // ✅ UPDATE TRANSAKSI
//     $transaksi->update([
//         'status_pembayaran' => $status,
//         'metode_pembayaran' => $request->payment_type,
//         'waktu_bayar' => $status === 'dibayar' ? now() : null,
//     ]);

//     // ✅ UPDATE ORDER (OPTIONAL)
//     $transaksi->order->update([
//         'status_pembayaran' => $status,
//     ]);

//     return response()->json(['message' => 'Callback OK']);
// }

// public function syncMidtransStatus($orderId)
// {
//     Config::$serverKey = config('services.midtrans.server_key');
//     Config::$isProduction = false;

//     $transaksi = Transaksi::where('order_id', $orderId)->first();

//     if (!$transaksi) {
//         return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
//     }

//     // 🔥 Ambil status dari Midtrans
//     $status = Transaction::status($transaksi->payment_reference);

//     // 🔥 Mapping KE ENUM DB
//     $map = [
//         'pending'    => 'menunggu_pembayaran',
//         'settlement' => 'dibayar',
//         'expire'     => 'kadaluarsa',
//         'cancel'     => 'dibatalkan',
//         'refund'     => 'dikembalikan',
//     ];

//     $statusDb = $map[$status->transaction_status] ?? 'menunggu_pembayaran';

//     // Update transaksi
//     $transaksi->update([
//         'status_pembayaran' => $statusDb,
//         'metode_pembayaran' => $status->payment_type ?? null,
//         'waktu_bayar' => $statusDb === 'dibayar' ? now() : null,
//     ]);

//     // Update order
//     $transaksi->order->update([
//         'status_pembayaran' => $statusDb,
//     ]);

//     return response()->json([
//         'message' => 'Status berhasil disinkronkan',
//         'midtrans_status' => $status->transaction_status,
//         'status_db' => $statusDb
//     ]);
// }




// public function getSnapToken($id)
// {
//     $order = Order::findOrFail($id);

//     // Kalau masih pending & token sudah ada → pakai ulang
//     if ($order->status_pembayaran === 'pending' && $order->snap_token) {
//         return response()->json([
//             'snap_token' => $order->snap_token
//         ]);
//     }

//     // VALIDASI NOMINAL
//     if (!$order->harga_final || $order->harga_final < 1) {
//         return response()->json([
//             'message' => 'Biaya tidak valid'
//         ], 422);
//     }

//     // KONFIGURASI MIDTRANS (BENAR)
//     Config::$serverKey = env('MIDTRANS_SERVER_KEY');
//     Config::$isProduction = false; // sandbox
//     Config::$isSanitized = true;
//     Config::$is3ds = true;

//     // PAYLOAD SNAP
//     $payload = [
//         'transaction_details' => [
//             'order_id' => 'ORDER-' . $order->id . '-' . time(),
//             'gross_amount' => (int) $order->harga_final,
//         ],
//         'item_details' => [
//             [
//                 'id' => $order->id,
//                 'price' => (int) $order->harga_final,
//                 'quantity' => 1,
//                 'name' => 'Pembayaran Order Laundry',
//             ]
//         ],
//         'customer_details' => [
//             'first_name' => $order->pengirim ?? 'User',
//             'email' => 'dummy@mail.com',
//         ],
//     ];

//     try {
//         $snapToken = Snap::getSnapToken($payload);

//         // SIMPAN TOKEN
//         $order->snap_token = $snapToken;
//         $order->status_pembayaran = 'pending';
//         $order->save();

//         return response()->json([
//             'snap_token' => $snapToken
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'message' => 'Gagal membuat Snap Token',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }


    public function show($id)
    {
        // Ambil data transaksi
        // $order = Order::with(['kurir'])->findOrFail($id);

        // Konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false; // Ganti ke true jika production
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Buat parameter Snap
        $params = [
            'transaction_details' => [
                'order_id' => $order->id,
                'gross_amount' => $order->harga_final, // pastikan ini integer, tanpa titik/koma
            ],
            'customer_details' => [
                'first_name' => $order->name ?? 'User',
                'email' => $order->email ?? 'user@example.com',
            ]
        ];

        // Buat Snap Token
        $snapToken = Snap::getSnapToken($params);

        // Kirim ke view atau langsung redirect
        return view('payment.show', compact('order', 'snapToken'));
    }
  public function handle(Request $request)
{
    \Log::info('MIDTRANS CALLBACK', $request->all());

    $serverKey = config('services.midtrans.server_key');

    $signatureKey = hash(
        'sha512',
        $request->order_id .
        $request->status_code .
        $request->gross_amount .
        $serverKey
    );

    if ($signatureKey !== $request->signature_key) {
        return response()->json(['message' => 'Invalid signature'], 403);
    }

    $transaksi = Transaksi::where('payment_reference', $request->order_id)->first();

    if (!$transaksi) {
        return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
    }

    $statusMap = [
        'pending'    => 'menunggu_pembayaran',
        'settlement' => 'dibayar',
        'expire'     => 'kadaluarsa',
        'cancel'     => 'dibatalkan',
        'refund'     => 'dikembalikan',
    ];

    $statusPembayaran = $statusMap[$request->transaction_status] ?? 'menunggu_pembayaran';

    $transaksi->update([
        'status_pembayaran' => $statusPembayaran,
        'metode_pembayaran' => $request->payment_type,
        'waktu_bayar' => $request->transaction_status === 'settlement'
            ? now()
            : null,
    ]);

    // OPTIONAL kalau order punya status bayar
    if ($transaksi->order) {
        $transaksi->order->update([
            'status_pembayaran' => $statusPembayaran,
        ]);
    }

    return response()->json(['message' => 'OK']);
}



public function manualUpdateStatus(Request $request)
{
    $order = Order::find($request->order_id);

    if (!$order) {
        return response()->json(['message' => 'Order tidak ditemukan'], 404);
    }

    // Buat / ambil transaksi
    $transaksi = Transaksi::firstOrCreate(
        ['order_id' => $order->id],
        ['total_bayar' => $order->total_harga]
    );

    // Update transaksi (SUMBER UTAMA)
    $transaksi->update([
        'status_pembayaran' => $request->transaction_status,
        'metode_pembayaran' => $request->payment_type,
        'payment_reference' => $request->order_id_midtrans ?? null,
        'waktu_bayar' => now(),
    ]);

    // Sinkron ke orders (MIRROR)
    $order->update([
        'status_pembayaran' => $transaksi->status_pembayaran
    ]);

    return response()->json([
        'message' => 'Status pembayaran berhasil diperbarui'
    ]);
}



public function setPaymentMethod(Request $request, $id)
{
    $request->validate([
        'payment_method' => 'required|in:cash,midtrans'
    ]);

    $order = Order::findOrFail($id);
    $order->payment_method = $request->payment_method;
    $order->save();

    return response()->json(['message' => 'Metode pembayaran disimpan']);
}


    // public function manualUpdateStatus(Request $request)
    // {
    //     // $order = Order::find($request->order_id);
    //         $order = Order::where('kode_order', $request->order_id)->first();
    //     if ($order) {
    //         $order->status_pembayaran = $request->transaction_status ?? 'settlement';
    //         // $order->payment_type = $request->payment_type ?? 'manual';
    //         $order->save();

    //         Log::info("Manual update: order_id={$request->order_id}, status={$order->status_pembayaran}");
    //         return response()->json(['message' => 'Status pembayaran berhasil diperbarui']);
    //     }

    //     return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
    // }

}
