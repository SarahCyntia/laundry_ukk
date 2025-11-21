<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;

class PelangganTransaksiController extends Controller
{
    public function index() {
        $pelanggan_id = auth()->id();
        $transaksi = Transaksi::with(['mitra', 'layanan'])
            ->where('pelanggan_id', $pelanggan_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($transaksi);
    }

    // public function printResi($id) {
    //     $transaksi = Transaksi::with(['mitra', 'layanan'])
    //         ->where('pelanggan_id', auth()->id())
    //         ->findOrFail($id);

    //     return view('resi', compact('transaksi'));
    // }

    public function show($id) {
        $transaksi = Transaksi::with(['mitra', 'layanan'])
            ->where('pelanggan_id', auth()->id())
            ->findOrFail($id);

        return response()->json($transaksi);
    }
    public function printResi($id)
{
    $transaksi = Transaksi::with(['pelanggan', 'layanan', 'mitra'])
        ->findOrFail($id);

    // Pastikan Mitra/Pelanggan hanya bisa akses resi mereka sendiri
    if(auth()->user()->role == 'mitra' && $transaksi->mitra_id != auth()->user()->mitra->id){
        abort(403);
    }
    if(auth()->user()->role == 'pelanggan' && $transaksi->pelanggan_id != auth()->id()){
        abort(403);
    }

    $pdf = Pdf::loadView('resi', compact('transaksi'));
    return $pdf->download("resi_laundry_{$transaksi->id}.pdf");
}
}
