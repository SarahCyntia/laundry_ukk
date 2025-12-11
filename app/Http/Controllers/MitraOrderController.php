<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Order;
use Illuminate\Http\Request;

class MitraOrderController extends Controller
{



    public function index()
    {
        $mitraId = auth()->user()->mitra_id ?? auth()->id();

        $orders = Order::where('mitra_id', $mitraId)
            ->with('pelanggan:id,name') // ambil nama pelanggan
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($o) {
                return [
                    'id' => $o->id,
                    'kode_order' => $o->kode_order,
                    'pelanggan_name' => $o->pelanggan->name ?? '-',
                    'status' => $o->status,
                    'berat_estimasi' => $o->berat_estimasi,
                    'berat_aktual' => $o->berat_aktual,
                    'harga_final' => $o->harga_final,
                    'catatan' => $o->catatan,
                    'created_at' => $o->created_at->format('Y-m-d H:i'),
                ];
            });

        return response()->json($orders);
    }

    // =============================
    // LIST ORDER MASUK
//     // =============================

    public function orderMasuk()
{
    $mitra = auth()->user()->mitra;

    // Jika $mitra berupa integer (user_id), convert ke object Mitra
    if (is_int($mitra)) {
        $mitra = Mitra::where('user_id', $mitra)->first();
    }

    if (!$mitra) {
        return response()->json(['message' => 'Data mitra tidak ditemukan'], 404);
    }

    $order = Order::where('mitra_id', $mitra->id)
        ->where('status', 'menunggu_konfirmasi_mitra')
        ->with(['pelanggan.user', 'jenis_layanan'])
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($order);
}


    //     public function orderMasuk(Request $request)
// {
//     $mitraId = auth()->user()->mitra?->id;

    //     if (!$mitraId) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Akun ini bukan mitra. Tidak dapat melihat order masuk.'
//         ], 403);
//     }

    //     $order = Order::where('mitra_id', $mitraId)
//         ->where('status', 'menunggu_konfirmasi_mitra')
//         ->with('pelanggan:id,name')
//         ->orderBy('created_at', 'desc')
//         ->get()
//         ->map(function ($o) {
//             return [
//                 'id' => $o->id,
//                 'kode_order' => $o->kode_order,
//                 'pelanggan_name' => $o->pelanggan->name ?? '-',
//                 'berat_estimasi' => $o->berat_estimasi,
//                 'catatan' => $o->catatan,
//             ];
//         });

    //     return response()->json($order);
// }


    // =============================
    // TERIMA ORDER
    // =============================
    public function accept($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'menunggu_konfirmasi') {
            return response()->json(['message' => 'Order tidak valid'], 400);
        }

        $order->status = 'diproses';
        $order->save();

        return response()->json(['message' => 'Order diterima']);
    }

    // =============================
    // TOLAK ORDER
    // =============================
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string',
        ]);

        $order = Order::findOrFail($id);

        if ($order->status !== 'menunggu_konfirmasi') {
            return response()->json(['message' => 'Order tidak valid'], 400);
        }

        $order->status = 'ditolak';
        $order->alasan_penolakan = $request->alasan;
        $order->save();

        return response()->json(['message' => 'Order ditolak']);
    }

    // =============================
    // LIST ORDER DIPROSES
    // =============================
    public function orderDiproses()
    {
        $mitraId = auth()->user()->mitra_id ?? auth()->id();

        $order = Order::where('mitra_id', $mitraId)
            ->whereIn('status', ['diproses', 'dicuci', 'dikeringkan', 'disetrika'])
            ->with('pelanggan:id,name')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($o) {
                return [
                    'id' => $o->id,
                    'kode_order' => $o->kode_order,
                    'pelanggan_name' => $o->pelanggan->name ?? '-',
                    'status' => $o->status,
                    'berat_estimasi' => $o->berat_estimasi,
                ];
            });

        return response()->json($order);
    }

    // =============================
    // UPDATE STATUS PROSES
    // diproses → dicuci → dikeringkan → disetrika → siap_diambil
    // =============================
    public function updateStatus($id)
    {
        $order = Order::findOrFail($id);

        $nextStep = [
            'diproses' => 'dicuci',
            'dicuci' => 'dikeringkan',
            'dikeringkan' => 'disetrika',
            'disetrika' => 'siap_diambil',
        ];

        if (!isset($nextStep[$order->status])) {
            return response()->json(['message' => 'Status tidak dapat diperbarui'], 400);
        }

        $order->status = $nextStep[$order->status];
        $order->save();

        return response()->json([
            'message' => 'Status diperbarui',
            'status_baru' => $order->status
        ]);
    }

    public function orderSiapDiambil()
    {
        $mitraId = auth()->user()->mitra_id ?? auth()->id();

        $order = Order::where('mitra_id', $mitraId)
            ->where('status', 'siap_diambil')
            ->with('pelanggan:id,name')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($o) {
                return [
                    'id' => $o->id,
                    'kode_order' => $o->kode_order,
                    'pelanggan_name' => $o->pelanggan->name ?? '-',
                    'berat_estimasi' => $o->berat_estimasi,
                ];
            });

        return response()->json($order);
    }


    public function TandaiSebagaiSelesai($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'siap_diambil') {
            return response()->json(['message' => 'Order tidak valid'], 400);
        }

        $order->status = 'selesai';
        $order->save();

        return response()->json(['message' => 'Order selesai & ditutup']);
    }

    public function orderSelesai()
    {
        $mitraId = auth()->user()->mitra_id ?? auth()->id();

        $order = Order::where('mitra_id', $mitraId)
            ->where('status', 'selesai')
            ->with('pelanggan:id,name')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($o) {
                return [
                    'id' => $o->id,
                    'kode_order' => $o->kode_order,
                    'pelanggan_name' => $o->pelanggan->name ?? '-',
                    'berat_estimasi' => $o->berat_estimasi,
                    'total_harga' => $o->total_harga,
                    'tanggal_selesai' => $o->updated_at->format('Y-m-d H:i'),
                ];
            });

        return response()->json($order);
    }

    public function notifOrderBaru()
{
    $mitraId = auth()->user()->mitra_id;

    $order = Order::with('pelanggan')
        ->where('mitra_id', $mitraId)
        ->where('status', 'menunggu_konfirmasi_mitra')
        ->get();

    return response()->json([
        'count' => $order->count(),
        'order' => $order
    ]);
}

    // public function notifOrderBaru()
    // {
    //     $mitraId = auth()->user()->mitra_id;

    //     $adaOrderBaru = Order::where('mitra_id', $mitraId)
    //         ->where('status', 'menunggu_konfirmasi_mitra')
    //         ->exists();

    //     return response()->json([
    //         'new_order' => $adaOrderBaru
    //     ]);
    // }


    public function pelangganDatang(Request $request)
    {
        $request->validate(['kode_order' => 'required']);

        $order = Order::where('kode_order', $request->kode_order)->first();

        if (!$order) {
            return response()->json(['message' => 'Kode order tidak ditemukan'], 404);
        }

        if ($order->status !== 'siap_diambil') {
            return response()->json(['message' => 'Order belum siap diambil'], 400);
        }

        $order->status = 'selesai';
        $order->save();

        return response()->json(['message' => 'Pelanggan sudah mengambil laundry']);
    }


}
