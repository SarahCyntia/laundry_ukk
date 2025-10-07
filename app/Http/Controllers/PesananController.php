<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function cekStatus($tracking_code)
    {
        // Ambil semua pesanan dengan tracking_code (bukan hanya first)
        $pesanan_list = Pesanan::with(['pelanggan', 'paket'])
            ->where('tracking_code', $tracking_code)
            ->orderBy('id', 'asc')
            ->get();

        if ($pesanan_list->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode tracking tidak ditemukan'
            ], 404);
        }

        // Hitung total berat & harga
        $total_berat = $pesanan_list->sum('berat');
        $total_harga = $pesanan_list->sum('harga');

        // Ambil nama paket, berat & harga per item
        $paket_names   = $pesanan_list->pluck('paket.nama')->toArray();
        $berat_values  = $pesanan_list->pluck('berat')->toArray();
        $harga_values  = $pesanan_list->pluck('harga')->toArray();

        // Status pembayaran dari pesanan terakhir
        $status_pembayaran = $pesanan_list->last()->status_pembayaran ?? 'sudah_dibayar';

        // Data pelanggan ambil dari pesanan pertama
        $pelanggan = $pesanan_list->first()->pelanggan ?? null;

        return response()->json([
            'success' => true,
            'data' => [
                'tracking_code'       => $tracking_code,
                'total_berat'         => $total_berat,
                'total_harga_pesanan' => $total_harga,
                'status_pembayaran'   => $status_pembayaran,
                'waktu_pesanan'       => $pesanan_list->first()->waktu,
                'paket_list'          => implode(', ', $paket_names),
                'berat_list'          => implode(', ', $berat_values),
                'harga_list'          => implode(', ', $harga_values),
                'harga_pesanan'       => $total_harga,
                'harga_custom'        => isset($pesanan_list->first()->harga_custom) 
                                           ? (float)$pesanan_list->first()->harga_custom 
                                           : 5000,
                'pelanggan'           => $pelanggan,
                'pesanan_detail'      => $pesanan_list
            ]
        ]);
    }
}

// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use App\Models\Pesanan;
// use Illuminate\Http\Request;

// class PesananController extends Controller
// {
//     public function cekStatus($tracking_code)
//     {
//         $pesanan = Pesanan::with(['pelanggan', 'paket'])
//             ->where('tracking_code', $tracking_code)
//             ->first();

//         if (!$pesanan) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Kode tracking tidak ditemukan'
//             ], 404);
//         }

//         return response()->json([
//             'success' => true,
//             'data' => $pesanan
//         ]);
//     }
// }
