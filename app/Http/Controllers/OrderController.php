<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function index()
    {
        $mitraId = auth()->user()->mitra_id ?? auth()->id();

        $order = Order::where('mitra_id', $mitraId)
            ->with('pelanggan:id,name', 'jenis_layanan') // ambil nama pelanggan
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

        return response()->json($order);
    }

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
        ]);

        $order = Order::create([
            'pelanggan_id' => $request->pelanggan_id,
            'mitra_id' => $request->mitra_id,
            'jenis_layanan_id' => $request->jenis_layanan_id,  // ⬅ Tambahkan
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


    /* ===========================
       2. MITRA MELIHAT ORDER
       =========================== */
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
        $order = Order::with(['pelanggan', 'mitra', 'jenisLayanan'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }


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

}
