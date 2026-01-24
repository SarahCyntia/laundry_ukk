<?php

namespace App\Http\Controllers;

use App\Exports\LaporanKeuanganExport;
use App\Models\Order;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class LaporanKeuanganController extends Controller
{
public function index(Request $request)
{
    $mitra = auth()->user()->mitra;

    // 1️⃣ JANGAN paginate dulu
    $query = Order::where('mitra_id', $mitra->id)
        ->with([
            'pelanggan.user:id,name',
            'jenis_layanan:id,nama_layanan',
            'mitra:id,nama_laundry',
            'transaksi'
        ]);

    // 2️⃣ Filter status order
    if ($request->status) {
        $query->where('status', $request->status);
    }

    if ($request->has('exclude_status')) {
        $query->whereNotIn('status', $request->exclude_status);
    }

    // 3️⃣ Filter periode
    if ($request->filter_period) {
        $now = now();

        switch ($request->filter_period) {
            case 'hari-ini':
    $query->whereHas('transaksi', function ($q) use ($now) {
        $q->whereDate('waktu_bayar', $now);
    });
    break;

            // case 'hari-ini':
            //     $query->whereDate('created_at', $now);
            //     break;

            case 'minggu-ini':
    $query->whereHas('transaksi', function ($q) use ($now) {
        $q->whereBetween('waktu_bayar', [
            $now->startOfWeek(),
            $now->endOfWeek()
        ]);
    });
    break;

            // case 'minggu-ini':
            //     $query->whereBetween('created_at', [
            //         $now->startOfWeek(),
            //         $now->endOfWeek()
            //     ]);
            //     break;

                case 'bulan-ini':
    $query->whereHas('transaksi', function ($q) use ($now) {
        $q->whereMonth('waktu_bayar', $now->month)
          ->whereYear('waktu_bayar', $now->year);
    });
    break;

            // case 'bulan-ini':
            //     $query->whereMonth('created_at', $now->month)
            //           ->whereYear('created_at', $now->year);
            //     break;

                case 'tahun-ini':
    $query->whereHas('transaksi', function ($q) use ($now) {
        $q->whereYear('waktu_bayar', $now->year);
    });
    break;

            // case 'tahun-ini':
            //     $query->whereYear('created_at', $now->year);
            //     break;

            case 'custom':
    if ($request->start_date && $request->end_date) {
        $query->whereHas('transaksi', function ($q) use ($request) {
            $q->whereBetween('waktu_bayar', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        });
    }
    break;

            // case 'custom':
            //     if ($request->start_date && $request->end_date) {
            //         $query->whereBetween('created_at', [
            //             $request->start_date . ' 00:00:00',
            //             $request->end_date . ' 23:59:59'
            //         ]);
            //     }
            //     break;
        }
    }

    // 4️⃣ Filter transaksi SUDAH DIBAYAR
    $query->whereHas('transaksi', function ($q) {
        $q->where('status_pembayaran', 'dibayar');
    });

    // 5️⃣ BARU eksekusi
    $orders = $query
        ->orderBy('created_at', 'desc')
        ->get(); // atau paginate()

    // 6️⃣ Mapping
    $data = $orders->map(function ($order) {
        return [
            'id' => $order->id,
            'created_at' => $order->created_at,
            'waktu_bayar' => $order->transaksi->waktu_bayar ?? null,
            'no_order' => $order->kode_order,
            'status' => $order->status,
            'total_harga' => $order->transaksi->total_bayar ?? 0,

            'pelanggan' => [
                'name' => $order->pelanggan->user->name ?? '-'
            ],

            'mitra' => [
                'nama_laundry' => $order->mitra->nama_laundry ?? '-'
            ],
        ];
    });

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}



private function baseQuery(Request $request)
{
    $mitra = auth()->user()->mitra;
    $now = now();

    $query = Order::where('mitra_id', $mitra->id)
        ->with(['pelanggan.user', 'mitra', 'jenis_layanan', 'transaksi'])
        ->whereHas('transaksi', function ($q) {
            $q->where('status_pembayaran', 'dibayar');
        });

    if ($request->filter_period) {
        switch ($request->filter_period) {
            case 'hari-ini':
                $query->whereHas('transaksi', fn ($q) =>
                    $q->whereDate('waktu_bayar', $now)
                );
                break;

            case 'minggu-ini':
                $query->whereHas('transaksi', fn ($q) =>
                    $q->whereBetween('waktu_bayar', [
                        $now->startOfWeek(),
                        $now->endOfWeek()
                    ])
                );
                break;

            case 'bulan-ini':
                $query->whereHas('transaksi', fn ($q) =>
                    $q->whereMonth('waktu_bayar', $now->month)
                      ->whereYear('waktu_bayar', $now->year)
                );
                break;

            case 'tahun-ini':
                $query->whereHas('transaksi', fn ($q) =>
                    $q->whereYear('waktu_bayar', $now->year)
                );
                break;

            case 'custom':
                if ($request->start_date && $request->end_date) {
                    $query->whereHas('transaksi', fn ($q) =>
                        $q->whereBetween('waktu_bayar', [
                            $request->start_date.' 00:00:00',
                            $request->end_date.' 23:59:59'
                        ])
                    );
                }
                break;
        }
    }

    return $query;
}





// public function exportPdf(Request $request)
// {
//     $orders = $this->baseQuery($request)->get();

//     $totalHarga = $orders->sum(fn ($o) => $o->transaksi->total_bayar ?? 0);

//     $pdf = Pdf::loadView('laporan.keuangan-pdf', [
//         'order' => $orders,
//         'totalOrder' => $orders->count(),
//         'totalHarga' => $totalHarga,
//         'periodTitle' => $request->filter_period ?? 'Semua Waktu',
//         'generatedAt' => now()->format('d M Y H:i')
//     ])->setPaper('A4', 'portrait');

//     return $pdf->download('laporan-keuangan-'.$request->filter_period.'.pdf');
// }


public function exportPdf(Request $request)
{
    $orders = $this->baseQuery($request)->get();

    $totalHarga = $orders->sum(fn ($o) => $o->transaksi->total_bayar ?? 0);

    $pdf = Pdf::loadView('laporan.keuangan-pdf', [
        'order' => $orders,
        'totalOrder' => $orders->count(),
        'totalHarga' => $totalHarga,
        'periodTitle' => $this->makePeriodTitle($request),
        'generatedAt' => now()->translatedFormat('d M Y H:i')
    ])->setPaper('A4', 'portrait');

    return $pdf->download('laporan-keuangan.pdf');
}


public function exportExcel(Request $request)
{
    $orders = $this->baseQuery($request)->get();

    return Excel::download(
        new LaporanKeuanganExport($orders),
        'laporan-keuangan.xlsx'
    );
}







private function makePeriodTitle(Request $request)
{
    $now = now();

    return match ($request->filter_period) {
        'hari-ini' =>
            'Hari Ini (' . $now->translatedFormat('d M Y') . ')',

        'minggu-ini' =>
            'Minggu Ini (' .
            $now->startOfWeek()->translatedFormat('d M Y') .
            ' – ' .
            $now->endOfWeek()->translatedFormat('d M Y') . ')',

        'bulan-ini' =>
            'Bulan Ini (' . $now->translatedFormat('F Y') . ')',

        'tahun-ini' =>
            'Tahun Ini (' . $now->format('Y') . ')',

        'custom' =>
            Carbon::parse($request->start_date)->translatedFormat('d M Y')
            . ' – ' .
            Carbon::parse($request->end_date)->translatedFormat('d M Y'),

        default => 'Semua Waktu',
    };
}


}
