<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Order;
use Illuminate\Http\Request;
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
                'catatan' => $o->catatan,
                'created_at' => $o->created_at->format('Y-m-d H:i'),
            ];
        });

        return response()->json($query);
    }



    /* ============================================
     *  2. CUSTOMER MEMBUAT ORDER + MIDTRANS
     * ============================================ */

    //sebelumnya
    public function store(Request $request)
{
    $request->validate([
        'pelanggan_id' => 'required',
        'mitra_id' => 'required',
        'jenis_layanan_id' => 'required|exists:jenis_layanan,id',
        'berat_estimasi' => 'nullable',
        'catatan' => 'nullable',
    ]);

    $pelanggan = auth()->user()->pelanggan;

    if (!$pelanggan) {
        return response()->json([
            'message' => 'Akun ini tidak memiliki data pelanggan'
        ], 400);
    }

    $order = Order::create([
        'pelanggan_id' => $pelanggan->id,
        'mitra_id' => $request->mitra_id,
        'jenis_layanan_id' => $request->jenis_layanan_id, // FIX
        'kode_order' => 'ORD-' . time(),
        'berat_estimasi' => $request->berat_estimasi,
        'catatan' => $request->catatan,
        'status' => 'menunggu_konfirmasi_mitra',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Order berhasil dibuat',
        'data' => $order
    ]);
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
        $order->status = 'diterima';
        $order->save();

        return response()->json(['message' => 'Order dikonfirmasi']);
    }


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

        $allowed = ['dicuci', 'dikeringkan', 'disetrika', 'siap_diambil', 'selesai'];

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

        $request->validate([
            'pelanggan_id' => 'nullable|numeric',
            'berat_estimasi' => 'nullable|numeric',
            'berat_aktual' => 'nullable|numeric',
            'harga_final' => 'nullable|numeric',
            'catatan' => 'nullable|string',
            'status' => 'required|string',
            'alasan_penolakan' => 'nullable|string',
        ]);

        $order->update($request->all());

        return response()->json(['message' => 'Order berhasil diperbarui']);
    }
}
