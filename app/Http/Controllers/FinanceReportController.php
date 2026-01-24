<?php

// app/Http/Controllers/Api/FinanceReportController.php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceReportController extends Controller
{
    public function getReport(Request $request)
    {
        $mitraId = Auth::id(); // ID mitra yang login
        $period = $request->get('period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Query transaksi yang sudah dibayar
        $query = Transaksi::with(['order.pelanggan', 'order.jenisLayanan'])
                    ->paid()
                    ->forMitra($mitraId);

        // Filter berdasarkan periode
        switch ($period) {
            case 'today':
                $query->today();
                $periodLabel = 'Hari Ini';
                break;
            case 'yesterday':
                $query->yesterday();
                $periodLabel = 'Kemarin';
                break;
            case 'this_week':
                $query->thisWeek();
                $periodLabel = 'Minggu Ini';
                break;
            case 'last_week':
                $query->lastWeek();
                $periodLabel = 'Minggu Kemarin';
                break;
            case 'this_month':
                $query->thisMonth();
                $periodLabel = 'Bulan Ini';
                break;
            case 'last_month':
                $query->lastMonth();
                $periodLabel = 'Bulan Kemarin';
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $query->dateRange($startDate, $endDate);
                    $periodLabel = Carbon::parse($startDate)->format('d M Y') . ' - ' . Carbon::parse($endDate)->format('d M Y');
                } else {
                    $periodLabel = 'Semua';
                }
                break;
            default:
                $periodLabel = 'Hari Ini';
        }

        $transaksi = $query->orderBy('waktu_bayar', 'desc')->get();

        // Hitung total pendapatan
        $totalPendapatan = $transaksi->sum('total_bayar');
        $totalTransaksi = $transaksi->count();

        // Breakdown per metode pembayaran
        $byPaymentMethod = $transaksi->groupBy('metode_pembayaran')->map(function($items, $method) {
            return [
                'jumlah_transaksi' => $items->count(),
                'total' => $items->sum('total_bayar')
            ];
        });

        // Breakdown per jenis layanan
        $byService = $transaksi->groupBy(function($item) {
            return $item->order->jenisLayanan->nama ?? 'Tidak Ada';
        })->map(function($items) {
            return [
                'jumlah_transaksi' => $items->count(),
                'total' => $items->sum('total_bayar')
            ];
        });

        // Rata-rata per transaksi
        $avgPerTransaction = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;

        // Format data transaksi untuk tampilan
        $formattedTransaksi = $transaksi->map(function($t) {
            return [
                'id' => $t->id,
                'order_id' => $t->order_id,
                'kode_order' => $t->order->kode_order ?? '-',
                'nama_pelanggan' => $t->order->pelanggan->nama ?? '-',
                'jenis_layanan' => $t->order->jenisLayanan->nama ?? '-',
                'berat_aktual' => $t->order->berat_aktual ?? 0,
                'total_bayar' => $t->total_bayar,
                'metode_pembayaran' => $t->metode_pembayaran,
                'waktu_bayar' => $t->waktu_bayar,
                'payment_reference' => $t->payment_reference,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $periodLabel,
                'summary' => [
                    'total_pendapatan' => $totalPendapatan,
                    'total_transaksi' => $totalTransaksi,
                    'rata_rata_per_transaksi' => round($avgPerTransaction, 2),
                ],
                'by_payment_method' => $byPaymentMethod,
                'by_service' => $byService,
                'transaksi' => $formattedTransaksi,
            ]
        ]);
    }

    public function getComparison(Request $request)
    {
        $mitraId = Auth::id();

        // Data bulan ini vs bulan lalu
        $thisMonth = Transaksi::paid()->forMitra($mitraId)->thisMonth()->get();
        $lastMonth = Transaksi::paid()->forMitra($mitraId)->lastMonth()->get();

        // Minggu ini vs minggu lalu
        $thisWeek = Transaksi::paid()->forMitra($mitraId)->thisWeek()->get();
        $lastWeek = Transaksi::paid()->forMitra($mitraId)->lastWeek()->get();

        // Hari ini vs kemarin
        $today = Transaksi::paid()->forMitra($mitraId)->today()->get();
        $yesterday = Transaksi::paid()->forMitra($mitraId)->yesterday()->get();

        return response()->json([
            'success' => true,
            'data' => [
                'monthly_comparison' => [
                    'this_month' => [
                        'pendapatan' => $thisMonth->sum('total_bayar'),
                        'transaksi' => $thisMonth->count(),
                    ],
                    'last_month' => [
                        'pendapatan' => $lastMonth->sum('total_bayar'),
                        'transaksi' => $lastMonth->count(),
                    ]
                ],
                'weekly_comparison' => [
                    'this_week' => [
                        'pendapatan' => $thisWeek->sum('total_bayar'),
                        'transaksi' => $thisWeek->count(),
                    ],
                    'last_week' => [
                        'pendapatan' => $lastWeek->sum('total_bayar'),
                        'transaksi' => $lastWeek->count(),
                    ]
                ],
                'daily_comparison' => [
                    'today' => [
                        'pendapatan' => $today->sum('total_bayar'),
                        'transaksi' => $today->count(),
                    ],
                    'yesterday' => [
                        'pendapatan' => $yesterday->sum('total_bayar'),
                        'transaksi' => $yesterday->count(),
                    ]
                ]
            ]
        ]);
    }

    public function getChartData(Request $request)
    {
        $mitraId = Auth::id();
        $type = $request->get('type', 'daily'); // daily, weekly, monthly

        switch ($type) {
            case 'daily':
                // 7 hari terakhir
                $data = [];
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $pendapatan = Transaksi::paid()
                        ->forMitra($mitraId)
                        ->whereDate('waktu_bayar', $date)
                        ->sum('total_bayar');
                    
                    $data[] = [
                        'label' => $date->format('d M'),
                        'value' => $pendapatan
                    ];
                }
                break;

            case 'weekly':
                // 4 minggu terakhir
                $data = [];
                for ($i = 3; $i >= 0; $i--) {
                    $start = Carbon::now()->subWeeks($i)->startOfWeek();
                    $end = Carbon::now()->subWeeks($i)->endOfWeek();
                    
                    $pendapatan = Transaksi::paid()
                        ->forMitra($mitraId)
                        ->whereBetween('waktu_bayar', [$start, $end])
                        ->sum('total_bayar');
                    
                    $data[] = [
                        'label' => 'Minggu ' . $start->format('d M'),
                        'value' => $pendapatan
                    ];
                }
                break;

            case 'monthly':
                // 6 bulan terakhir
                $data = [];
                for ($i = 5; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $pendapatan = Transaksi::paid()
                        ->forMitra($mitraId)
                        ->whereMonth('waktu_bayar', $date->month)
                        ->whereYear('waktu_bayar', $date->year)
                        ->sum('total_bayar');
                    
                    $data[] = [
                        'label' => $date->format('M Y'),
                        'value' => $pendapatan
                    ];
                }
                break;
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getTopCustomers(Request $request)
    {
        $mitraId = Auth::id();
        $limit = $request->get('limit', 10);

        $topCustomers = Transaksi::paid()
            ->forMitra($mitraId)
            ->select('order.pelanggan_id', DB::raw('COUNT(*) as total_transaksi'), DB::raw('SUM(transaksi.total_bayar) as total_belanja'))
            ->join('order', 'transaksi.order_id', '=', 'order.id')
            ->groupBy('order.pelanggan_id')
            ->orderBy('total_belanja', 'desc')
            ->limit($limit)
            ->with('order.pelanggan')
            ->get()
            ->map(function($item) {
                return [
                    'nama' => $item->order->pelanggan->nama ?? 'Unknown',
                    'email' => $item->order->pelanggan->email ?? '-',
                    'total_transaksi' => $item->total_transaksi,
                    'total_belanja' => $item->total_belanja,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $topCustomers
        ]);
    }
}