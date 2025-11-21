<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // pastikan sudah install barryvdh/laravel-dompdf

class MitraTransaksiController extends Controller
{
    // List semua transaksi Mitra
    public function index() {
        $mitra_id = auth()->user()->mitra->id;

        $transaksi = Transaksi::with(['pelanggan', 'layanan'])
            ->where('mitra_id', $mitra_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($transaksi);
    }

    // Update status transaksi
    public function updateStatus(Request $request, $id) {
        $request->validate([
            'status' => 'required|in:pending,diterima,dicuci,selesai,ditolak'
        ]);

        $transaksi = Transaksi::findOrFail($id);

        // Pastikan transaksi milik Mitra
        if ($transaksi->mitra_id !== auth()->user()->mitra->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $transaksi->status = $request->status;
        $transaksi->save();

        return response()->json(['message' => 'Status updated']);
    }

    // Print resi transaksi
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

// class MitraTransaksiController extends Controller
// {
//     public function index() {
//         $mitra_id = auth()->user()->mitra->id;
//         $transaksi = Transaksi::with(['pelanggan', 'layanan'])
//             ->where('mitra_id', $mitra_id)
//             ->orderBy('created_at', 'desc')
//             ->get();

//         return response()->json($transaksi);
//     }

//     public function updateStatus(Request $request, $id) {
//         $request->validate([
//             'status' => 'required|in:menunggu-jemput,dijemput,dalam-perjalanan,selesai'
//         ]);

//         $transaksi = Transaksi::findOrFail($id);
//         if($transaksi->mitra_id !== auth()->user()->mitra->id) {
//             return response()->json(['message' => 'Unauthorized'], 403);
//         }

//         $transaksi->status = $request->status;
//         $transaksi->save();

//         return response()->json(['message' => 'Status updated']);
//     }


//     public function printResi($id)
// {
//     $transaksi = Transaksi::with(['pelanggan', 'layanan', 'mitra'])
//         ->findOrFail($id);

//     // Pastikan Mitra/Pelanggan hanya bisa akses resi mereka sendiri
//     if(auth()->user()->role == 'mitra' && $transaksi->mitra_id != auth()->user()->mitra->id){
//         abort(403);
//     }
//     if(auth()->user()->role == 'pelanggan' && $transaksi->pelanggan_id != auth()->id()){
//         abort(403);
//     }

//     $pdf = Pdf::loadView('resi', compact('transaksi'));
//     return $pdf->download("resi_laundry_{$transaksi->id}.pdf");
// }
// }
