<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Order;
use Illuminate\Http\Request;
use Midtrans\Snap;
// use PSpell\Config;
    use Midtrans\Config;

class Orderlama2Controller extends Controller
{

    // public function index()
    // {
    //     $userId = auth()->user()->id;
    //     $mitraId = Mitra::where('user_id', $userId)->first();
    //     $query = Order::query()->with(['pelanggan', 'mitra', 'jenis_layanan']);
    //     $order = Order::where('mitra_id', $mitraId->id)
    //         ->with('pelanggan:id,name', 'jenis_layanan') // ambil nama pelanggan
    //         ->orderBy('created_at', 'desc')
    //         ->get()
    //         ->map(function ($o) {
    //             return [
    //                 'id' => $o->id,
    //                 'kode_order' => $o->kode_order,
    //                 'pelanggan_name' => $o->pelanggan->name ?? '-',
    //                 'status' => $o->status,
    //                 'berat_estimasi' => $o->berat_estimasi,
    //                 'berat_aktual' => $o->berat_aktual,
    //                 'harga_final' => $o->harga_final,
    //                 'catatan' => $o->catatan,
    //                 'created_at' => $o->created_at->format('Y-m-d H:i'),
    //             ];
    //         });
    // // FILTER BERDASARKAN MITRA
    // if ($request->mitra_id) {
    //     $query->where('mitra_id', $request->mitra_id);
    // }
    // return response()->json($query->paginate(10));

    //     // return response()->json($order);
    // }

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
    ->orderBy('created_at', 'desc')
    ->paginate($per);

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
        'catatan' => $o->catatan,
        'created_at' => $o->created_at->format('Y-m-d H:i'),
    ];
});


    return response()->json($query);
}
//sebelumnya2
//     public function index(Request $request)
// {
//     $per = $request->per ?? 10;
//     $page = $request->page ? $request->page - 1 : 0;

//     $user = auth()->user();
//     $mitra = Mitra::where('user_id', $user->id)->first();

//     if (!$mitra) {
//         return response()->json(['message' => 'Mitra tidak ditemukan'], 404);
//     }

//     $query = Order::where('mitra_id', $mitra->id)
//         ->with(['pelanggan', 
//         'jenis_layanan:id,nama_layanan',
//         'mitra',])
//         // ->with(['pelanggan','jenis_layanan'])
//         ->orderBy('created_at', 'desc')
//         ->paginate($per);

//     // Tambahkan nomor urut
//     $startNo = ($query->currentPage() - 1) * $per + 1;

//     $query->getCollection()->transform(function ($o) use (&$startNo) {
//         return [
//             'no' => $startNo++,
//             'id' => $o->id,
//             'kode_order' => $o->kode_order,
//             'pelanggan' => $o->pelanggan->name ?? '-',
//             'jenis_layanan' => $o->jenis_layanan->nama ?? '-',
//             'status' => $o->status,
//             'berat_estimasi' => $o->berat_estimasi,
//             'berat_aktual' => $o->berat_aktual,
//             'harga_final' => $o->harga_final,
//             'catatan' => $o->catatan,
//             'created_at' => $o->created_at->format('Y-m-d H:i'),
//         ];
//     });

//     return response()->json($query);
// }


//lama sebelum1
//     public function index(Request $request)
// {
//     $user = auth()->user();
//     $mitra = Mitra::where('user_id', $user->id)->first();

//     if (!$mitra) {
//         return response()->json(['message' => 'Mitra tidak ditemukan'], 404);
//     }

//     $query = Order::where('mitra_id', $mitra->id)
//         ->with(['pelanggan:id,name', 'jenis_layanan'])
//         ->orderBy('created_at', 'desc')
//         ->paginate(10);

//     // RUBAH DATA PAGINATE MENJADI DATA CUSTOM
//     $query->getCollection()->transform(function ($o) {
//         return [
//             'id' => $o->id,
//             'kode_order' => $o->kode_order,
//             'pelanggan_name' => $o->pelanggan->name ?? '-',
//             'jenis_layanan' => $o->jenis_layanan->nama ?? '-',
//             'status' => $o->status,
//             'berat_estimasi' => $o->berat_estimasi,
//             'berat_aktual' => $o->berat_aktual,
//             'harga_final' => $o->harga_final,
//             'catatan' => $o->catatan,
//             'created_at' => $o->created_at->format('Y-m-d H:i'),
//         ];
//     });


    
//     return response()->json($query);
// }


    /* ===========================
       1. CUSTOMER MEMBUAT ORDER
       =========================== */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'pelanggan_id' => 'required',
    //         'mitra_id' => 'required',
    //         'berat_estimasi' => 'nullable',
    //         'catatan' => 'nullable',
    //     ]);

    //     $order = Order::create([
    //         'pelanggan_id' => $request->pelanggan_id,
    //         'mitra_id' => $request->mitra_id,
    //         'kode_order' => 'ORD-' . time(),
    //         'berat_estimasi' => $request->berat_estimasi,
    //         'catatan' => $request->catatan,
    //         'status' => 'menunggu_konfirmasi_mitra',
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Order berhasil dibuat',
    //         'data' => $order
    //     ]);
    // }




public function store(Request $request)
{
    $request->validate([
        'pelanggan_id' => 'required',
        'mitra_id' => 'required',
        'jenis_layanan_id' => 'required|exists:jenis_layanan,id',
        'berat_estimasi' => 'nullable',
        'catatan' => 'nullable',
        'harga_final' => 'required|numeric', // ⬅️ WAJIB untuk Midtrans
    ]);

    $pelanggan = auth()->user()->pelanggan;

    if (!$pelanggan) {
        return response()->json([
            'message' => 'Akun ini tidak memiliki data pelanggan'
        ], 400);
    }

    // 1️⃣ SIMPAN ORDER
    $order = Order::create([
        'pelanggan_id' => $pelanggan->id,
        'mitra_id' => $request->mitra_id,
        'jenis_layanan_id' => $request->jenis_layanan_id,
        'kode_order' => 'ORD-' . time(),
        'berat_estimasi' => $request->berat_estimasi,
        'catatan' => $request->catatan,
        'harga_final' => $request->harga_final,
        'status' => 'menunggu_konfirmasi_mitra',
        'status_pembayaran' => 'pending',
    ]);

    // 2️⃣ KONFIGURASI MIDTRANS
    Config::$serverKey = config('services.midtrans.serverKey');
    Config::$isProduction = config('services.midtrans.isProduction');
    Config::$isSanitized = true;
    Config::$is3ds = true;

    // 3️⃣ PARAMETER MIDTRANS
    $params = [
        'detail_transaksi' => [
            'order_id' => 'LAUNDRY-' . $order->id,
            'gross_amount' => $order->harga_final,
        ],
        'detail_pelanggan' => [
            'first_name' => $pelanggan->nama ?? 'Pelanggan',
            'phone' => $pelanggan->no_hp ?? '-',
        ],
    ];

    // 4️⃣ GENERATE SNAP TOKEN
    $snapToken = Snap::getSnapToken($params);

    // 5️⃣ SIMPAN SNAP TOKEN
    $order->update([
        'snap_token' => $snapToken,
    ]);

    // 6️⃣ KIRIM KE FRONTEND (LANGSUNG BISA BAYAR)
    return response()->json([
        'success' => true,
        'message' => 'Order berhasil dibuat',
        'snap_token' => $snapToken,
        'data' => $order
    ]);
}





//sebelumnya
//     public function store(Request $request)
// {
//     $request->validate([
//         'pelanggan_id' => 'required',
//         'mitra_id' => 'required',
//         'jenis_layanan_id' => 'required|exists:jenis_layanan,id',
//         'berat_estimasi' => 'nullable',
//         'catatan' => 'nullable',
//     ]);

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
//     ]);

//     return response()->json([
//         'success' => true,
//         'message' => 'Order berhasil dibuat',
//         'data' => $order
//     ]);
// }

 
       public function orderMitra()
    {
        $mitraId = auth()->user()->mitra_id;

        $order = Order::where('mitra_id', $mitraId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($order);
    }

    /* ===========================
       3. MITRA TERIMA ORDER
       =========================== */
    public function accept($id)
    {
        $order = Order::findOrFail($id);
        $order->status = 'diterima';
        $order->save();

        return response()->json(['message' => 'Order diterima']);
    }

    /* ===========================
       4. MITRA TOLAK ORDER
       =========================== */
       public function konfirmasi($id)
{
    $order = Order::findOrFail($id);
    $order->status = 'diterima';
    $order->save();

    return response()->json(['message' => 'Order dikonfirmasi']);
}


public function tolak(Request $request, $id)
{
    $order = Order::findOrFail($id);

    $request->validate([
        'alasan_penolakan' => 'required'
    ]);

    $order->status = 'ditolak';
    $order->alasan_penolakan = $request->alasan_penolakan;
    $order->save();

    return response()->json(['message' => 'Order ditolak']);
}

    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required'
        ]);

        $order = Order::findOrFail($id);

        $order->status = 'ditolak';
        $order->alasan_penolakan = $request->alasan;
        $order->save();

        return response()->json(['message' => 'Order ditolak']);
    }

    /* ===========================
       5. CUSTOMER ANTAR LAUNDRY
       =========================== */
    public function pelangganDatang(Request $request, $id)
    {
        $request->validate([
            'berat_aktual' => 'required',
            'harga_final' => 'required'
        ]);

        $order = Order::findOrFail($id);

        $order->berat_aktual = $request->berat_aktual;
        $order->harga_final = $request->harga_final;
        $order->waktu_pelanggan_antar = now();
        $order->status = 'menunggu_konfirmasi_mitra';
        $order->save();

        return response()->json(['message' => 'Berat aktual dicatat & order diproses']);
    }

    /* ===========================
       6. UPDATE STATUS PROSES
       dicuci → dikeringkan → disetrika → siap_diambil
       =========================== */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required'
        ]);

        $allowed = ['dicuci', 'dikeringkan', 'disetrika', 'siap_diambil', 'selesai'];

        if (!in_array($request->status, $allowed)) {
            return response()->json(['error' => 'Status tidak valid'], 422);
        }

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json(['message' => 'Status diperbarui']);
    }


//     public function updateStatus(Request $request, $id)
// {
//     $request->validate([
//         'status' => 'required'
//     ]);

//     $order = Order::findOrFail($id);
//     $order->status = $request->status;
//     $order->save();

//     return response()->json(['success' => true]);
// }

    /* ===========================
       7. ORDER SELESAI (SUDAH DIAMBIL)
       =========================== */
    public function selesai($id)
    {
        $order = Order::findOrFail($id);

        $order->status = 'selesai';
        $order->waktu_diambil = now();
        $order->save();

        return response()->json(['message' => 'Order selesai & sudah diambil']);
    }

    /* ===========================
   SHOW DETAIL ORDER
   =========================== */
   public function show($id)
{
    $order = Order::with(['pelanggan', 'mitra', 'jenis_layanan'])
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
        ],

        'pelanggan' => [
            'id' => $order->pelanggan?->id,
            'name' => $order->pelanggan?->name,
            'phone' => $order->pelanggan?->phone,
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

    // public function show($id)
    // {
    //     $order = Order::with(['pelanggan', 'mitra', 'jenis_layanan'])
    //         ->findOrFail($id);

    //     return response()->json([
    //         'success' => true,
    //         'data' => $order
    //     ]);
    // }


    public function getOrderPelanggan(Request $request)
    {
        try {
            $pelangganId = $request->user()->id;

            $orders = Order::with(['mitra', 'jenisLayanan', 'pelanggan'])
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

    public function update(Request $request, $id)
{
    $order = Order::findOrFail($id);

    $request->validate([
        'pelanggan_id' => 'nullable|string',
        'berat_estimasi' => 'nullable|numeric',
        'berat_aktual' => 'nullable|numeric',
        'harga_final' => 'nullable|numeric',
        'catatan' => 'nullable|string',
        'status' => 'required|string',
        'alasan_penolakan' => 'nullable|string',
    ]);

    $order->update([
        'pelanggan_id' => $request->pelanggan_id,
        'berat_estimasi' => $request->berat_estimasi,
        'berat_aktual' => $request->berat_aktual,
        'harga_final' => $request->harga_final,
        'catatan' => $request->catatan,
        'status' => $request->status,
        'alasan_penolakan' => $request->alasan_penolakan,
        'waktu_pelanggan_antar' => $request->waktu_pelanggan_antar,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Order berhasil diperbarui',
        'data' => $order
    ]);
}


}
