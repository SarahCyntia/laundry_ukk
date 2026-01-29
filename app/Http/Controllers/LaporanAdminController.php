<?php

namespace App\Http\Controllers;

use App\Exports\LaporanKeuanganExport;
use App\Models\Order;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class LaporanAdminController extends Controller
{
public function index(Request $request)
{
    $period = $request->period;

    $query = Order::with([
        'pelanggan.user',
        'mitra',
        'transaksi'
    ])
    ->whereHas('transaksi', function ($q) {
        $q->where('status_pembayaran', 'dibayar');
    });

    if ($period === 'today') {
        $query->whereDate('created_at', now());
    } elseif ($period === 'week') {
        $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    } elseif ($period === 'month') {
        $query->whereMonth('created_at', now()->month);
    }

    $data = $query->get()->map(function ($item) {
        return [
            'id' => $item->id,
            'kode_order' => $item->kode_order,
            'nama_pelanggan' => $item->pelanggan->user->name ?? '-',
            'nama_mitra' => $item->mitra->nama_laundry ?? '-',
            'metode_pembayaran' => $item->transaksi->metode_pembayaran ?? '-',
            'status_pembayaran' => $item->transaksi->status_pembayaran,
            'total_harga' => $item->harga_final,
        ];
    });

    return response()->json([
        'data' => $data
    ]);
}




}
