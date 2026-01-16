<?php

namespace App\Http\Controllers;

use App\Models\JenisLayanan;
use App\Models\Mitra;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Dotenv\Util\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Midtrans\Snap;
use Midtrans\Config;

class OrderController extends Controller
{

    /* ============================================
     *  1. LIST ORDER MITRA (PAGINATION + RELASI)
     * ============================================ */
    public function index(Request $request)
    {
        $per = $request->per ?? 10;

        $user = auth()->user();
        $mitra = Mitra::where('user_id', $user->id)->first();

        if (!$mitra) {
            return response()->json(['message' => 'Mitra tidak ditemukan'], 404);
        }

        $query = Order::where('mitra_id', $mitra->id)
            ->with([
                'pelanggan:id,user_id',
                'pelanggan.user:id,name',
                'jenis_layanan:id,nama_layanan',
                'mitra:id,user_id,nama_laundry',
            ])
            ->when($request->status, function ($query, $status) {
                // Jika parameter 'status' ada, filter berdasarkan status tersebut
                $query->where('status', $status);
            })
            ->when($request->has('exclude_status'), function ($query) use ($request) {
                $query->whereNotIn('status', $request->exclude_status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($per);


        // Nomor urut
        $start = ($query->currentPage() - 1) * $per + 1;
        $query->getCollection()->transform(function ($o) use (&$start) {
    return [
        'no' => $start++,
        'id' => $o->id,
        'kode_order' => $o->kode_order,

        'pelanggan' => [
            'id' => $o->pelanggan->id ?? null,
            'name' => $o->pelanggan->user->name ?? "-",
        ],

        'mitra' => [
            'id' => $o->mitra->id ?? null,
            'nama_laundry' => $o->mitra->nama_laundry ?? "-",
        ],

        'jenis_layanan' => [
            'id' => $o->jenis_layanan->id ?? null,
            'nama_layanan' => $o->jenis_layanan->nama_layanan ?? "-",
        ],

        'status' => $o->status,
        'berat_estimasi' => $o->berat_estimasi,
        'berat_aktual' => $o->berat_aktual,
        'harga_final' => $o->harga_final,
        'alasan_penolakan' => $o->alasan_penolakan,
        'estimasi_selesai' => $o->estimasi_selesai,
        'estimasi_jam' => $o->estimasi_jam,
        'catatan' => $o->catatan,
        'foto_struk' => $o->foto_struk,

        // 🔥 INI YANG KURANG
        'waktu_pelanggan_antar' => $o->waktu_pelanggan_antar
            ? $o->waktu_pelanggan_antar->format('Y-m-d H:i')
            : null,

        'created_at' => $o->created_at->format('Y-m-d H:i'),
    ];
});


        // $query->getCollection()->transform(function ($o) use (&$start) {
        //     return [
        //         'no' => $start++,
        //         'id' => $o->id,
        //         'kode_order' => $o->kode_order,

        //         'pelanggan' => [
        //             'id' => $o->pelanggan->id ?? null,
        //             'name' => $o->pelanggan->user->name ?? "-",
        //         ],

        //         'mitra' => [
        //             'id' => $o->mitra->id ?? null,
        //             'nama_laundry' => $o->mitra->nama_laundry ?? "-",
        //         ],

        //         'jenis_layanan' => [
        //             'id' => $o->jenis_layanan->id ?? null,
        //             'nama_layanan' => $o->jenis_layanan->nama_layanan ?? "-",
        //         ],

        //         'status' => $o->status,
        //         'berat_estimasi' => $o->berat_estimasi,
        //         'berat_aktual' => $o->berat_aktual,
        //         'harga_final' => $o->harga_final,
        //         'alasan_penolakan' => $o->alasan_penolakan,
        //         'estimasi_selesai' => $o->estimasi_selesai,
        //         'estimasi_jam' => $o->estimasi_jam,
        //         'catatan' => $o->catatan,
        //         'foto_struk' => $o->foto_struk,
        //         'created_at' => $o->created_at->format('Y-m-d H:i'),
        //     ];
        // });



        return response()->json($query);
    }



    /* ============================================
     *  2. CUSTOMER MEMBUAT ORDER + MIDTRANS
     * ============================================ */

    //sebelumnya
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'pelanggan_id' => 'required',
    //         'mitra_id' => 'required',
    //         'jenis_layanan_id' => 'required|exists:jenis_layanan,id',
    //         'berat_estimasi' => 'nullable',
    //         'catatan' => 'nullable',
    //         'foto_struk' => 'nullable',

    //     ]);

    //     $layanan = JenisLayanan::findOrFail($request->jenis_layanan_id);
    //     $hargaPerKg = $layanan->harga; // contoh: 8000

    //     // 🔥 HITUNG TOTAL
    //     $hargaFinal = $request->berat_estimasi * $hargaPerKg;

    //     $pelanggan = auth()->user()->pelanggan;

    //     if (!$pelanggan) {
    //         return response()->json([
    //             'message' => 'Akun ini tidak memiliki data pelanggan'
    //         ], 400);
    //     }

    //     $order = Order::create([
    //         'pelanggan_id' => $pelanggan->id,
    //         'mitra_id' => $request->mitra_id,
    //         'jenis_layanan_id' => $request->jenis_layanan_id, // FIX
    //         'kode_order' => 'ORD-' . time(),
    //         'berat_estimasi' => $request->berat_estimasi,
    //         'catatan' => $request->catatan,
    //         'status' => 'menunggu_konfirmasi_mitra',
    //           'harga_final' => $hargaFinal, 
    //     ]);

    //      if ($request->hasFile('foto_struk')) {
    //     $path = $request->file('foto_struk')->store('order_struk', 'public');
    //     $order->foto_struk = $path;
    //     $order->save();
    // }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Order berhasil dibuat',
    //         'data' => $order
    //     ]);
    // }


    public function store(Request $request)
{
    $request->validate([
        'mitra_id' => 'required|exists:mitra,id',
        'jenis_layanan_id' => 'required|exists:jenis_layanan,id',
        'berat_estimasi' => 'required|numeric|min:1',
        'catatan' => 'nullable|string',
        'foto_struk' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Ambil pelanggan dari user login
    $pelanggan = auth()->user()->pelanggan;

    if (!$pelanggan) {
        return response()->json([
            'message' => 'Akun ini tidak memiliki data pelanggan'
        ], 400);
    }

    // Ambil layanan
    $layanan = JenisLayanan::findOrFail($request->jenis_layanan_id);
    $hargaPerKg = $layanan->harga;

    // Hitung total harga
    $hargaFinal = $request->berat_estimasi * $hargaPerKg;

    // Simpan order
    $order = Order::create([
        'pelanggan_id' => $pelanggan->id,
        'mitra_id' => $request->mitra_id,
        'jenis_layanan_id' => $request->jenis_layanan_id,
        'kode_order' => 'ORD-' . now()->format('YmdHis') . '-' . rand(100,999),
        'berat_estimasi' => $request->berat_estimasi,
        'catatan' => $request->catatan,
        'status' => 'menunggu_konfirmasi_mitra',
        'harga_final' => $hargaFinal,
    ]);

    // Upload foto struk (opsional)
    if ($request->hasFile('foto_struk')) {
        $path = $request->file('foto_struk')->store('order_struk', 'public');
        $order->update([
            'foto_struk' => $path
        ]);
    }

    // 🔥 BUAT TRANSAKSI (WAJIB)
    $order->transaksi()->create([
        'total_bayar' => $hargaFinal,
        'status_pembayaran' => 'belum_dibayar'
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Order berhasil dibuat',
        'data' => $order->load('transaksi')
    ], 201);
}



    //BUAT BESOK
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'mitra_id' => 'required',
    //         'jenis_layanan_id' => 'required|exists:jenis_layanan,id',
    //         'berat_estimasi' => 'nullable|numeric',
    //         'catatan' => 'nullable|string',
    //         'harga_final' => 'required|numeric',
    //     ]);

    //     $pelanggan = auth()->user()->pelanggan;

    //     if (!$pelanggan) {
    //         return response()->json([
    //             'message' => 'Akun ini tidak memiliki data pelanggan'
    //         ], 400);
    //     }

    //     // SIMPAN ORDER
    //     $order = Order::create([
    //         'pelanggan_id' => $pelanggan->id,
    //         'mitra_id' => $request->mitra_id,
    //         'jenis_layanan_id' => $request->jenis_layanan_id,
    //         'kode_order' => 'ORD-' . time(),
    //         'berat_estimasi' => $request->berat_estimasi,
    //         'catatan' => $request->catatan,
    //         'harga_final' => $request->harga_final,
    //         'status' => 'menunggu_konfirmasi_mitra',
    //         'status_pembayaran' => 'pending',
    //     ]);

    //     // KONFIGURASI MIDTRANS
    //     Config::$serverKey = config('services.midtrans.serverKey');
    //     Config::$isProduction = config('services.midtrans.isProduction');
    //     Config::$isSanitized = true;
    //     Config::$is3ds = true;

    //     // PARAMETER MIDTRANS
    //     $params = [
    //         'transaction_details' => [
    //             'order_id' => 'LAUNDRY-' . $order->id,
    //             'gross_amount' => $order->harga_final,
    //         ],
    //         'customer_details' => [
    //             'first_name' => $pelanggan->nama ?? 'Pelanggan',
    //             'phone' => $pelanggan->no_hp ?? '-',
    //         ],
    //     ];

    //     // SNAP TOKEN
    //     $snapToken = Snap::getSnapToken($params);

    //     $order->update([
    //         'snap_token' => $snapToken,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Order berhasil dibuat',
    //         'snap_token' => $snapToken,
    //         'data' => $order
    //     ]);
    // }




    /* ============================================
     *  3. MITRA MENERIMA ORDER
     * ============================================ */
    public function accept($id)
    {
        $order = Order::findOrFail($id);
        $order->status = 'diterima';
        $order->save();

        return response()->json(['message' => 'Order diterima']);
    }


    /* ============================================
     *  4. MITRA KONFIRMASI / TOLAK ORDER
     * ============================================ */
    public function konfirmasi($id)
    {
        $order = Order::findOrFail($id);

        $order->status = "ditunggu_mitra";
        $order->waktu_pelanggan_antar = now()->addHours(2); // pelanggan harus antar dalam 2 jam
        $order->estimasi_selesai = now()->addHours($order->jenis_layanan->estimasi_jam);

        $order->save();

        // 🔔 KIRIM WA KE PELANGGAN
        Http::post("https://api.fonnte.com/send", [
            "target" => $order->pelanggan->user->phone,
            "message" =>
                "Hai {$order->pelanggan->user->name}, order laundry Anda *DITERIMA*.
             Silakan antar laundry *sebelum jam {$order->waktu_pelanggan_antar->format('H:i')}*.
             Estimasi selesai: *{$order->estimasi_selesai->format('H:i')}*.
             Kode Order: {$order->kode_order}.
             Laundry: {$order->mitra->nama_laundry}.",
            "token" => env("FONNTE_KEY")
        ]);

        return response()->json(['message' => 'Order diterima']);
    }

    //sebelumnya
    // public function konfirmasi($id)
    // {
    //     $order = Order::findOrFail($id);
    //     $order->status = 'diterima';
    //     $order->save();

    //     return response()->json(['message' => 'Order dikonfirmasi']);
    // }


    public function tolak(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required'
        ]);

        $order = Order::findOrFail($id);
        $order->update([
            'status' => 'ditolak',
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        return response()->json(['message' => 'Order ditolak']);
    }



    /* ============================================
     *  5. CUSTOMER ANTAR LAUNDRY
     * ============================================ */
    public function pelangganDatang(Request $request, $id)
    {
        $request->validate([
            'berat_aktual' => 'required|numeric',
            'harga_final' => 'required|numeric'
        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'berat_aktual' => $request->berat_aktual,
            'harga_final' => $request->harga_final,
            'waktu_pelanggan_antar' => now(),
            'status' => 'menunggu_konfirmasi_mitra',
        ]);

        return response()->json(['message' => 'Berat aktual dicatat & order diproses']);
    }




    /* ============================================
     *  6. UPDATE STATUS PROSES
     * ============================================ */
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required']);

        $allowed = ['diterima','dicuci', 'dikeringkan', 'disetrika', 'siap_diambil', 'selesai'];

        if (!in_array($request->status, $allowed)) {
            return response()->json(['error' => 'Status tidak valid'], 422);
        }

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json(['message' => 'Status diperbarui']);
    }




    /* ============================================
     *  7. ORDER SELESAI
     * ============================================ */
    public function selesai($id)
    {
        $order = Order::findOrFail($id);

        $order->update([
            'status' => 'selesai',
            'waktu_diambil' => now(),
        ]);

        return response()->json(['message' => 'Order selesai & sudah diambil']);
    }




    /* ============================================
     *  8. DETAIL ORDER
     * ============================================ */
    public function show($id)
    {
        $order = Order::with(['pelanggan.user', 'mitra', 'jenis_layanan'])
            ->findOrFail($id);

        return response()->json([
            'order' => [
                'id' => $order->id,
                'kode_order' => $order->kode_order,
                'berat_estimasi' => $order->berat_estimasi,
                'berat_aktual' => $order->berat_aktual,
                'harga_final' => $order->harga_final,
                'catatan' => $order->catatan,
                'status' => $order->status,
                'alasan_penolakan' => $order->alasan_penolakan,
                'waktu_pelanggan_antar' => $order->waktu_pelanggan_antar,
                'waktu_diambil' => $order->waktu_diambil,
                'foto_struk' => $order->foto_struk,
            ],

            'pelanggan' => [
                'id' => $order->pelanggan?->id,
                'name' => $order->pelanggan?->user?->name,
                'phone' => $order->pelanggan?->no_hp,
                'alamat' => $order->pelanggan?->alamat,
            ],

            'mitra' => [
                'id' => $order->mitra?->id,
                'nama_laundry' => $order->mitra?->nama_laundry,
            ],

            'jenis_layanan' => [
                'id' => $order->jenis_layanan?->id,
                'nama_layanan' => $order->jenis_layanan?->nama_layanan,
            ]
        ]);
    }




    /* ============================================
     *  9. ORDER PELANGGAN SENDIRI
     * ============================================ */
    public function getOrderPelanggan(Request $request)
    {
        try {
            $pelangganId = auth()->user()->pelanggan->id;

            $orders = Order::with(['mitra', 'jenis_layanan', 'pelanggan'])
                ->where('pelanggan_id', $pelangganId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data transaksi'
            ], 500);
        }
    }




    /* ============================================
     *  10. UPDATE ORDER (ADMIN/MITRA)
     * ============================================ */


public function update(Request $request, $id)
{
    $order = Order::findOrFail($id);

    if (in_array($order->status, ['selesai'])) {
    return response()->json([
        'message' => 'Order tidak bisa diedit karena sudah selesai'
    ], 403);
}


// if ($order->waktu_diambil && !$request->hasFile('foto_struk')) {
//     return response()->json([
//         'message' => 'Order tidak bisa diedit karena sudah diambil'
//     ], 403);
// }

    // if ($order->waktu_pelanggan_antar || $order->waktu_diambil) {
    //     return response()->json([
    //         'message' => 'Order tidak bisa diedit karena sudah diantar atau diambil'
    //     ], 403);
    // }

    $request->validate([
        'pelanggan_id' => 'nullable|numeric',
        'berat_estimasi' => 'nullable|numeric',
        'berat_aktual' => 'nullable|numeric',
        'harga_final' => 'nullable|numeric',
        'catatan' => 'nullable|string',
        'status' => 'nullable|string', // 🔥 FIX
        'alasan_penolakan' => 'nullable|string',
        'foto_struk' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $order->update($request->except('foto_struk'));

    if ($request->hasFile('foto_struk')) {
        if ($order->foto_struk) {
            Storage::disk('public')->delete($order->foto_struk);
        }

        $path = $request->file('foto_struk')->store('order_struk', 'public');
        $order->foto_struk = $path;
        $order->save();
    }

    return response()->json([
        'message' => 'Order berhasil diperbarui'
    ]);
}

    // public function update(Request $request, $id)
    // {
    //     $order = Order::findOrFail($id);

    //     if ($order->waktu_pelanggan_antar || $order->waktu_diambil) {
    //         return response()->json([
    //             'message' => 'Order tidak bisa diedit karena sudah diantar atau diambil'
    //         ], 403);
    //     }

    //     $request->validate([
    //         'pelanggan_id' => 'nullable|numeric',
    //         'berat_estimasi' => 'nullable|numeric',
    //         'berat_aktual' => 'nullable|numeric',
    //         'harga_final' => 'nullable|numeric',
    //         'catatan' => 'nullable|string',
    //         'status' => 'required|string',
    //         'alasan_penolakan' => 'nullable|string',
    //         'foto_struk' => 'nullable|string',
    //     ]);

    //     $order->update($request->all());

    //     return response()->json(['message' => 'Order berhasil diperbarui']);
    // }

      public function payment(Request $request)
    {
        $biaya = $request->order('biaya');

        if (!$biaya || !is_numeric($biaya) || $biaya <= 0) {
            return response()->json([
                'message' => 'Biaya ongkir belum valid',
            ], 400);
        }

        // $order_id = 'ONGKIR-' . now()->timestamp . '-' . Str::random(4);
        $order_id = 'ORDER-' . now()->format('Ymd') . '-' . rand(1000, 9999);

        $params = [
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => (int) $biaya,
            ],
            'item_details' => [
                [
                    'id' => $order_id,
                    'price' => (int) $biaya,
                    'quantity' => 1,
                    'name' => 'Biaya Ongkir Transaksi',
                ]
            ],
            'customer_details' => [
                'first_name' => $request->order('pengirim') ?? 'User',
                'email' => 'default@email.com',
            ],
            'callbacks' => [
                // 'finish' => url('/payment/success'),
                'finish' => env('APP_URL') . '/#/payment/success',
                'unfinish' => url('/payment/failed'),
                'error' => url('/payment/error'),
            ]
        ];

        $auth = base64_encode(env('MIDTRANS_SERVER_KEY'));
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Basic $auth",
        ])->withBody(json_encode($params), 'application/json')
            ->post('https://app.sandbox.midtrans.com/snap/v1/transactions');

        $data = json_decode($response->body());

        if (!isset($data->redirect_url)) {
            return response()->json([
                'message' => 'Gagal membuat link pembayaran Midtrans',
                'error' => $data,
            ], 500);
        }

        return response()->json([
            'redirect_url' => $data->redirect_url,
            'order_id' => $order_id,
        ]);
    }

    // public function createSnap(Request $request)
    // {
    //     $validated = $request->validate([
    //         'pelanggan_id' => 'required|string',
    //         'jenis_layanan_id' => 'required|string',
    //         'berat_aktual' => 'required|string',
    //         'harga_final' => 'required|numeric',
    //         'biaya' => 'required|numeric|min:1000',
    //     ]);

    //     $orderId = 'ORDER-' . Str::uuid();

    //     $payload = [
    //         'transaction_details' => [
    //             'order_id' => $orderId,
    //             'gross_amount' => (int) $validated['biaya'],
    //         ],
    //         'item_details' => [
    //             [
    //                 'id' => 'ongkir',
    //                 'price' => (int) $validated['biaya'],
    //                 'quantity' => 1,
    //                 'name' => 'Ongkir Kurir',
    //             ]
    //         ],
    //         'customer_details' => [
    //             'first_name' => $validated['pelanggan_id'],
    //             'email' => 'user@example.com',
    //         ],
    //         'callbacks' => [
    //             'finish' => url('/payment/callback'),
    //         ],
    //         'custom_field1' => json_encode($validated), // simpan data sementara
    //     ];

    //     $auth = base64_encode(env('MIDTRANS_SERVER_KEY'));

    //     $res = Http::withHeaders([
    //         'Authorization' => "Basic $auth",
    //         'Content-Type' => 'application/json',
    //     ])->post('https://app.sandbox.midtrans.com/snap/v1/transactions', $payload);

    //     $body = json_decode($res->body());

    //     if (isset($body->token)) {
    //         return response()->json([
    //             'snap_token' => $body->token,
    //         ]);
    //     }

    //     return response()->json([
    //         'message' => 'Gagal membuat token pembayaran',
    //         'error' => $body,
    //     ], 500);
    // }

    public function getSnapToken($id)
    {
        // $transaksii = Transaksii::with(['pengguna'])->findOrFail($id);
        $order = Order::where('id', $id)->firstOrFail();
        $order->status_pembayaran = 'belum dibayar';
        $order->save();

        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => $order->no_kode,
                'gross_amount' => (int) $order->biaya,
            ],
            'customer_details' => [
                'first_name' => $order->pengirim,
                // 'email' => $order->pengguna->email ?? 'user@gmail.com',
                // 'email' => $order->pengguna_id ? ($order->pengguna->email ?? 'user@gmail.com') : 'user@gmail.com',
                'email' => optional($order->pengirim)->email ?: 'user@gmail.com',
            ]
        ];

        $snapToken = Snap::getSnapToken($params);
        return response()->json(['snap_token' => $snapToken]);
    }
    public function handleCallback(Request $request)
    {
        $notif = $request->all();

        if (
            isset($notif['transaction_status']) &&
            $notif['transaction_status'] === 'settlement'
        ) {
            // Ambil data form dari custom_field1
            $data = json_decode($notif['custom_field1'], true);

            // Simpan transaksi ke DB
            $trans = new Order();
            $trans->fill($data);
            $trans->status = 'dibayar';
            $trans->save();

            // Simpan riwayat pembayaran
            $pay = new Order();
            $pay->Order_id = $trans->id;
            $pay->external_id = $notif['order_id'];
            $pay->status = 'success';
            $pay->save();
        }

        return response()->json(['message' => 'Callback diproses']);
    }

public function downloadKode($noKode)
{
    $data = Order::with(['pelanggan', 'mitra', 'jenis_layanan'])
        ->where('kode_order', $noKode)
        ->firstOrFail();

        Pdf::setOption([
    'dpi' => 72,
    'defaultFont' => 'Courier',
]);

    return Pdf::loadView('cetak-kode-pdf', compact('data'))
     ->setPaper([0, 0, 164, 300], 'portrait') // ±58mm
    
        ->download("STRUK-{$data->kode_order}.pdf");
}


//  public function downloadKode($noKode)
//     {
//         $data = Order::where('kode_order', $noKode)->first();

//         if (!$data) {
//             abort(404, 'Data tidak ditemukan');
//         }

//         // Gunakan view yang sama
//         $pdf = Pdf::loadView('cetak-kode-pdf', compact('data'));
//         $pdf->setPaper([165, 566], 'portrait'); // 58mm x 200mm

//         return $pdf->download("struk-{$noKode}.pdf"); // download otomatis
//     }
   


 public function cekStatus($kode_order)
    {
        $order = Order::where('kode_order', $kode_order)->first();

        if (!$order) {
            return response()->json([
                'message' => 'Kode order tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'kode_order' => $order->kode_order,
            'status' => $order->status,
            'status_label' => $this->statusLabel($order->status),
        ]);
    }

    private function statusLabel($status)
    {
        return match ($status) {
            'menunggu' => 'Menunggu Konfirmasi',
            'diproses' => 'Sedang Diproses',
            'dicuci'   => 'Sedang Dicuci',
            'disetrika'=> 'Sedang Disetrika',
            'selesai'  => 'Laundry Selesai',
            'diantar'  => 'Sedang Diantar',
            default    => 'Status Tidak Diketahui',
        };
    }

//     public function cekStatus($kode)
// {
//     $order = Order::where('kode_order', $kode)->first();

//     if (!$order) {
//         return response()->json([
//             'message' => 'Data tidak ditemukan'
//         ], 404);
//     }

//     return response()->json([
//         'kode_order' => $order->kode_order,
//         'status' => $order->status
//     ]);
// }





 public function destroy($id)
    {
        $order = Order::findOrFail($id);
        if ($order->photo) {
            Storage::disk('public')->delete($order->photo);
        }

        $order->delete();

        return response()->json([
            'success' => true
        ]);
    }
    
}
