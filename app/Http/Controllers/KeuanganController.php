<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMitraRequest;
use App\Http\Requests\UpdateMitraRequest;
use App\Models\Mitra;
use App\Models\Order;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
class KeuanganController extends Controller
{
    public function ringkasan()
{
    $mitraId = auth()->user()->mitra_id;

    $totalPendapatan = Order::where('mitra_id', $mitraId)
        ->where('status', 'selesai')
        ->sum('total_harga');

    $totalPencairan = Pencairan::where('mitra_id', $mitraId)
        ->sum('jumlah');

    return response()->json([
        'total_pendapatan' => $totalPendapatan,
        'total_pencairan' => $totalPencairan,
        'saldo' => $totalPendapatan - $totalPencairan,
    ]);
}

public function riwayat()
{
    $mitraId = auth()->user()->mitra_id;

    $data = Pencairan::where('mitra_id', $mitraId)
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($data);
}


public function ajukan(Request $r)
{
    $mitraId = auth()->user()->mitra_id;

    $saldo = Order::where('mitra_id', $mitraId)
        ->where('status', 'selesai')
        ->sum('total_harga')
        - Pencairan::where('mitra_id', $mitraId)->sum('jumlah');

    if ($r->jumlah > $saldo) {
        return response()->json(['message' => 'Saldo tidak mencukupi'], 400);
    }

    Pencairan::create([
        'mitra_id' => $mitraId,
        'jumlah' => $r->jumlah,
        'status' => 'menunggu',
    ]);

    return response()->json(['message' => 'Pencairan diajukan!']);
}


}