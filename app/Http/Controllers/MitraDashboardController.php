<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Order;

class MitraDashboardController extends Controller
{
    public function index()
    {
        $mitraId = auth()->user()->id;

        // Hitung order berdasarkan status
        $order = [
            'menunggu_konfirmasi_mitra' => Order::where('mitra_id', $mitraId)
                ->where('status', 'menunggu_konfirmasi_mitra')
                ->count(),

            'diproses' => Order::where('mitra_id', $mitraId)
                ->where('status', 'diproses')
                ->count(),

            'siap_diambil' => Order::where('mitra_id', $mitraId)
                ->where('status', 'siap_diambil')
                ->count(),

            'selesai' => Order::where('mitra_id', $mitraId)
                ->whereDate('updated_at', Carbon::today())
                ->where('status', 'selesai')
                ->count(),
        ];

        // Pendapatan hari ini
        $todayRevenue = Order::where('mitra_id', $mitraId)
            ->where('status', 'selesai')
            ->whereDate('updated_at', Carbon::today())
            ->sum('harga_final');

        // Statistik mingguan (chart)
        $weekly = [];
        $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

        foreach ($days as $index => $day) {
            $weekly[] = [
                'day' => $day,
                'count' => Order::where('mitra_id', $mitraId)
                    ->where('status', 'selesai')
                    ->whereDate('updated_at', Carbon::now()->startOfWeek()->addDays($index))
                    ->count()
            ];
        }

        // Order terbaru
        $recentOrders = Order::where('mitra_id', $mitraId)
            ->orderBy('id', 'DESC')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'customer' => $item->nama_pelanggan ?? 'Pelanggan',
                    'time' => $item->created_at->diffForHumans(),
                    'status' => $item->status,
                ];
            });

        return response()->json([
            'order' => $order,
            'weekly' => $weekly,
            'today_revenue' => $todayRevenue,
            'recent_orders' => $recentOrders,
        ]);
    }

    public function notifOrder()
    {
        $mitraId = auth()->user()->id;

        $new = Order::where('mitra_id', $mitraId)
            ->where('status', 'menunggu_konfirmasi_mitra')
            ->whereDate('created_at', now())
            ->count();

        return response()->json([
            'new_order' => $new > 0,
            'count' => $new
        ]);
    }
}
