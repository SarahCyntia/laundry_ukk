<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TransaksiMitraController extends Controller
{
    // 🔹 LIST DATA TRANSAKSI MILIK MITRA
    public function index()
    {
        $mitraId = Auth::user()->mitra->id; // sesuaikan dengan relasi Anda

        $data = Transaksi::with([
            'pelanggan',
            'mitra',
            'kurir',
            'jenisLayanan'
        ])
        ->where('mitra_id', $mitraId)
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'data' => $data
        ]);
    }

    // 🔹 DETAIL TRANSAKSI
    public function show($id)
    {
        $mitraId = Auth::user()->mitra->id;

        $data = Transaksi::with([
            'pelanggan',
            'mitra',
            'kurir',
            'jenisLayanan'
        ])
        ->where('mitra_id', $mitraId)
        ->findOrFail($id);

        return response()->json([
            'data' => $data
        ]);
    }

    // 🔹 UPDATE TRANSAKSI OLEH MITRA
    public function update(Request $request, $id)
    {
        $mitraId = Auth::user()->mitra->id;

        $transaksi = Transaksi::where('mitra_id', $mitraId)->findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,diproses,selesai',
            'total_harga' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        $transaksi->status = $request->status;
        $transaksi->total_harga = $request->total_harga;
        $transaksi->catatan = $request->catatan;

        $transaksi->save();

        return response()->json([
            'message' => 'Transaksi berhasil diperbarui!',
            'data' => $transaksi
        ]);
    }


    public function printResi($id)
{
    $transaksi = Transaksi::with(['pelanggan', 'mitra', 'layanan'])
        ->where('mitra_id', auth()->user()->mitra->id)
        ->findOrFail($id);

    $pdf = Pdf::loadView('pdf.resi-transaksi', compact('transaksi'));

    return $pdf->download("resi-transaksi-{$transaksi->id}.pdf");
}
}
