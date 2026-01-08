<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Pelanggan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PelangganOrderController extends Controller
{
    // pelanggan sudah mengantar baju ke laundry
    // public function sudahAntar(Request $request, Order $order)
    // {
    //     // pastikan order milik pelanggan yang login
    //     Log::info('Order : ', [$order->pelanggan_id]);
    //     Log::info('Auth : ', ['' => auth()->id()]);

    //     $pelanggan = Pelanggan::find($order->pelanggan_id)->first();
    //     Log::info('Pelanggan :', [$pelanggan]);

    //     if ($pelanggan->user_id !== auth()->id()) {
    //         return response()->json([
    //             'message' => 'Forbidden'
    //         ], 403);
    //     }

    //     // validasi status
    //    if ($order->status !== 'diterima') {
    //     return response()->json([
    //         'message' => 'Order sudah diantar atau tidak valid'
    //     ], 400);
    // }

    //     $order->update([
    //         'waktu_pelanggan_antar' => Carbon::now(),
    //         'status' => 'diproses'
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Baju berhasil diantar ke laundry'
    //     ]);
    // }
//sebelumnya
// public function sudahAntar(Request $request, Order $order)
// {
//     // pastikan order milik pelanggan login
//     $pelanggan = Pelanggan::find($order->pelanggan_id);

//     if (!$pelanggan || $pelanggan->user_id !== auth()->id()) {
//         return response()->json([
//             'message' => 'Forbidden'
//         ], 403);
//     }

//     // validasi status
//     if ($order->status !== 'diterima') {
//         return response()->json([
//             'message' => 'Order sudah diantar atau tidak valid'
//         ], 400);
//     }

//     // validasi file struk
//     $request->validate([
//         'foto_struk' => 'required|image|mimes:jpg,jpeg,png|max:2048'
//     ]);

//     // upload file
//     $path = $request->file('foto_struk')->store('order_struk', 'public');

//     // update order
//     $order->update([
//         'waktu_pelanggan_antar' => now(),
//         'status' => 'diproses',
//         'foto_struk' => $path
//     ]);

//     return response()->json([
//         'success' => true,
//         'message' => 'Baju berhasil diantar ke laundry',
//         'foto_struk_url' => asset('storage/' . $path)
//     ]);
// }

public function sudahAntar(Request $request, Order $order)
{
    // pastikan order milik pelanggan login
    $pelanggan = Pelanggan::find($order->pelanggan_id);

    if (!$pelanggan || $pelanggan->user_id !== auth()->id()) {
        return response()->json([
            'message' => 'Forbidden'
        ], 403);
    }

    // validasi status (opsional: cek hanya boleh upload jika diterima)
    if ($order->status !== 'ditunggu_mitra') {
        return response()->json([
            'message' => 'Order sudah diantar atau tidak valid'
        ], 400);
    }

    // validasi file struk
    $request->validate([
        'foto_struk' => 'required|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    // upload file
    $path = $request->file('foto_struk')->store('order_struk', 'public');

    // update order — status TIDAK diubah
    $order->update([
        'waktu_pelanggan_antar' => now(),
        'foto_struk' => $path
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Baju berhasil diantar ke laundry',
        'foto_struk_url' => asset('storage/' . $path)
    ]);
}



    // pelanggan sudah mengambil cucian
    public function sudahAmbil(Request $request, Order $order)
    {
        if ($order->pelanggan_id !== auth()->id()) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        if ($order->status !== 'selesai') {
            return response()->json([
                'message' => 'Cucian belum selesai'
            ], 400);
        }

        $order->update([
            'waktu_diambil' => Carbon::now(),
            'status' => 'diambil'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cucian berhasil diambil'
        ]);
    }
}
