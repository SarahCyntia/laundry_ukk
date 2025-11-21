<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransaksilamaController extends Controller
{
    /**
     * 🔹 Menampilkan semua transaksi milik mitra yang login
     */
    public function index(Request $request)
{
    $mitraId = $request->user()->mitra_id;

    $transaksi = Transaksi::with([
        'dataPelanggan:id,nama_pelanggan,no_hp,alamat', // ambil data pelanggan
        'pegawai:id,nama_pegawai',                     // ambil data pegawai
        'layananPrioritas:id,nama_prioritas,biaya',    // ambil layanan prioritas
        'detailTransaksi.jenisItem:id,nama_item',      // ambil item seperti baju, selimut
        'detailTransaksi.jenisLayanan:id,nama_layanan' // ambil jenis layanan: cuci, setrika, dll
    ])
    ->where('mitra_id', $mitraId)
    ->orderByDesc('created_at')
    ->get();

    return response()->json([
        'success' => true,
        'message' => 'Daftar transaksi berhasil diambil',
        'data' => $transaksi
    ]);
}

    /**
     * 🔹 Menyimpan transaksi baru untuk mitra yang login
     */
    public function store(Request $request)
    {
        $mitraId = $request->user()->mitra_id;

        $validated = $request->validate([
            'layanan_prioritas_id' => 'required|exists:layanan_prioritas,id',
            'pelanggan_id' => 'required|exists:data_pelanggan,id',
            'pegawai_id' => 'required|exists:pegawai_laundry,id',
            'total_biaya_layanan' => 'required|numeric',
            'total_biaya_prioritas' => 'required|numeric',
            'total_biaya_layanan_tambahan' => 'required|numeric',
            'total_bayar_akhir' => 'required|numeric',
            'jenis_pembayaran' => 'required|string',
            'bayar' => 'required|numeric',
            'kembalian' => 'required|numeric',
            'status' => 'required|string',
        ]);

        $transaksi = Transaksi::create([
            'id' => Str::uuid(),
            'nota_layanan' => 'LDR-' . strtoupper(Str::random(6)),
            'nota_pelanggan' => 'PEL-' . strtoupper(Str::random(6)),
            'waktu' => now(),
            'mitra_id' => $mitraId,
            ...$validated
        ]);

        return response()->json([
            'message' => 'Transaksi berhasil disimpan',
            'data' => $transaksi
        ]);
    }

    /**
     * 🔹 Menampilkan detail transaksi tertentu
     */
    public function show($id, Request $request)
    {
        $mitraId = $request->user()->mitra_id;

        $transaksi = Transaksi::where('mitra_id', $mitraId)
            ->where('id', $id)
            ->with(['dataPelanggan', 'pegawai', 'layananPrioritas'])
            ->firstOrFail();

        return response()->json($transaksi);
    }

    /**
     * 🔹 Update status atau data transaksi
     */
    public function update(Request $request, $id)
    {
        $mitraId = $request->user()->mitra_id;

        $transaksi = Transaksi::where('mitra_id', $mitraId)->findOrFail($id);

        $transaksi->update($request->all());

        return response()->json([
            'message' => 'Transaksi berhasil diperbarui',
            'data' => $transaksi
        ]);
    }

    /**
     * 🔹 Hapus transaksi
     */
    public function destroy(Request $request, $id)
    {
        $mitraId = $request->user()->mitra_id;

        $transaksi = Transaksi::where('mitra_id', $mitraId)->findOrFail($id);
        $transaksi->delete();

        return response()->json(['message' => 'Transaksi berhasil dihapus']);
    }
}
