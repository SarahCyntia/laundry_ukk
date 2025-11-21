<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiTracking;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Get all transactions
     */
    public function index()
    {
        $data = Transaksi::with(['pelanggan', 'mitra', 'jenis_layanan'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($data);
    }

    public function getUserTransaksi()
{
    $userId = auth()->id();

    $transaksi = Transaksi::with('mitra', 'pelanggan')
        ->where('user_id', $userId)
        ->orderBy('created_at', 'DESC')
        ->get();

    return response()->json($transaksi);
}


    /**
     * Store new transaction
     */
    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required',
            'mitra_id' => 'required',
            'jenis_layanan_id' => 'required',
            'berat' => 'required|numeric',
            'harga' => 'required|numeric',
            'biaya_kurir' => 'required|numeric',
            'total_harga' => 'required|numeric',
            'alamat_jemput' => 'required',
            'alamat_antar' => 'required',
        ]);

        $transaksi = Transaksi::create([
            'pelanggan_id' => $request->pelanggan_id,
            'mitra_id' => $request->mitra_id,
            'kurir_id' => $request->kurir_id ?? null,
            'jenis_layanan_id' => $request->jenis_layanan_id,
            'berat' => $request->berat,
            'harga' => $request->harga,
            'biaya_kurir' => $request->biaya_kurir,
            'total_harga' => $request->total_harga,
            'status' => 'menunggu jemput',
            'catatan' => $request->catatan,
            'alamat_jemput' => $request->alamat_jemput,
            'alamat_antar' => $request->alamat_antar,
            'tanggal_jemput' => $request->tanggal_jemput,
        ]);

        // auto tracking
        TransaksiTracking::create([
            'transaksi_id' => $transaksi->id,
            'status' => 'menunggu jemput',
            'keterangan' => 'Pesanan dibuat & menunggu kurir menjemput'
        ]);

        return response()->json([
            'message' => 'Transaksi berhasil dibuat',
            'data' => $transaksi
        ]);
    }

    /**
     * Show transaction detail
     */
    public function show($id)
    {
        $data = Transaksi::with(['pelanggan', 'mitra', 'kurir', 'layanan', 'tracking'])
            ->findOrFail($id);

        return response()->json($data);
    }

    /**
     * Update transaction (pilih kurir / update status / update data)
     */
    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $transaksi->update($request->all());

        // Jika status berubah → tambah tracking otomatis
        if ($request->has('status')) {
            TransaksiTracking::create([
                'transaksi_id' => $transaksi->id,
                'status' => $request->status,
                'keterangan' => $request->keterangan ?? 'Status diperbarui'
            ]);
        }

        return response()->json([
            'message' => 'Transaksi berhasil diupdate',
            'data' => $transaksi
        ]);
    }

    /**
     * Delete transaction
     */
    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();

        return response()->json([
            'message' => 'Transaksi berhasil dihapus'
        ]);
    }
}
