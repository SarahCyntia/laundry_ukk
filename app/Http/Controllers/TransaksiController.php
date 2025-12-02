<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiTracking;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Ambil semua transaksi untuk admin/mitra
     */
    public function index()
    {
        $data = Transaksi::with(['pelanggan.user', 'mitra', 'jenis_layanan'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($data);
        }


    /**
     * Transaksi milik pelanggan yang sedang login
     */
    public function getUserTransaksi()
    {
        $userId = auth()->id();

        $transaksi = Transaksi::with(['mitra', 'pelanggan', 'jenis_layanan'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($transaksi);
    }


    /**
     * Membuat transaksi baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'mitra_id' => 'required|exists:mitra,id',
            'jenis_layanan_id' => 'required|exists:jenis_layanan,id',
            'berat' => 'required|numeric',
            'harga' => 'required|numeric',
            'total_harga' => 'required|numeric',
            'alamat_jemput' => 'required|string',
            'alamat_antar' => 'required|string',
            'tanggal_jemput' => 'nullable|date',
            'catatan' => 'nullable|string'
        ]);

        $transaksi = Transaksi::create([
            'pelanggan_id' => $request->pelanggan_id,
            'mitra_id' => $request->mitra_id,
            'kurir_id' => null, // tidak digunakan lagi
            'jenis_layanan_id' => $request->jenis_layanan_id,
            'berat' => $request->berat,
            'harga' => $request->harga,
            'total_harga' => $request->total_harga,
            'status' => 'menunggu diterima', // status pertama
            'catatan' => $request->catatan,
            'alamat_jemput' => $request->alamat_jemput,
            'alamat_antar' => $request->alamat_antar,
            'tanggal_jemput' => $request->tanggal_jemput,
        ]);

        // Tracking otomatis
        TransaksiTracking::create([
            'transaksi_id' => $transaksi->id,
            'status' => 'menunggu diterima',
            'keterangan' => 'Pesanan dibuat & menunggu mitra menerima'
        ]);

        return response()->json([
            'message' => 'Transaksi berhasil dibuat',
            'data' => $transaksi
        ]);
    }


    /**
     * Detail transaksi lengkap
     */
    public function show($id)
    {
        $data = Transaksi::with(['pelanggan.user', 'mitra', 'jenis_layanan', 'tracking'])
            ->findOrFail($id);

        return response()->json($data);
    }


    /**
     * Update transaksi untuk merubah status atau data lain
     */
    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $request->validate([
            'status' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        // update data yang boleh berubah
        $transaksi->update($request->only([
            'status',
            'berat',
            'harga',
            'total_harga',
            'catatan',
            'alamat_jemput',
            'alamat_antar',
            'tanggal_jemput'
        ]));

        // tracking otomatis jika status berubah
        if ($request->status) {
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
     * Menghapus transaksi
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
