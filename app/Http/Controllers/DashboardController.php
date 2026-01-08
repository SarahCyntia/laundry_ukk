<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    /**
     * API Dashboard Mitra
     */
    public function getData(Request $request)
    {
        $user = auth()->user();

        // Jika user tidak punya mitra
        if (!$user->mitra) {
            return response()->json([
                'order' => [
                    'menunggu_konfirmasi_mitra' => 0,
                    'diproses' => 0,
                    'siap_diambil' => 0,
                    'selesai' => 0,
                ],
                'today_revenue' => 0,
                'weekly' => [],
                'pesananTerbaru' => [],
            ]);
        }

        $mitraId = $user->mitra->id;
        $baseQuery = Order::where('mitra_id', $mitraId);
// =====================
// WEEKLY STAT (7 HARI)
// =====================
Carbon::setLocale('id');

$weekly = [];

$start = Carbon::now()->startOfWeek(Carbon::MONDAY);

for ($i = 0; $i < 7; $i++) {
    $date = $start->copy()->addDays($i);

    $weekly[] = [
        'day' => $date->translatedFormat('D'), // Sen, Sel, Rab
        'count' => (clone $baseQuery)
            ->whereDate('waktu', $date)
            ->count(),
    ];
}



        /**
         * =====================
         * STATISTIK ORDER
         * =====================
         */
        $orderStat = [
            'menunggu_konfirmasi_mitra' => (clone $baseQuery)->where('status','menunggu_konfirmasi_mitra')->count(),
            'diproses' => (clone $baseQuery)->where('status','diproses')->count(),
            'siap_diambil' => (clone $baseQuery)->where('status','siap_diambil')->count(),
            'selesai' => (clone $baseQuery)->where('status','selesai')->count(),
        ];

        /**
         * =====================
         * PESANAN (5 / SEMUA)
         * =====================
         */
        $queryOrder = (clone $baseQuery)->latest('created_at');

        // kalau TIDAK klik "Lihat Semua"
        if (!$request->boolean('all')) {
            $queryOrder->limit(5);
        }

        $pesananTerbaru = (clone $baseQuery)
    ->latest('waktu')
    ->limit(5)
    ->get()
    ->map(fn ($item) => [
        'id' => $item->id,
        'kode_order' => $item->kode_order, // ✅ INI YANG HILANG
        'pelanggan_id' => $item->pelanggan_id,
        'time' => Carbon::parse($item->waktu)->format('H:i'),
        'status' => $item->status,
    ]);


        /**
         * =====================
         * RESPONSE
         * =====================
         */
        return response()->json([
            'order' => $orderStat,
            'today_revenue' => (clone $baseQuery)
                ->where('status','selesai')
                ->where('status_pembayaran','settlement')
                ->sum('harga_final'),
            'weekly' => $weekly, // ✅ SEKARANG ADA ISI
            'pesananTerbaru' => $pesananTerbaru,
        ]);
    }

    /**
     * Update Status Toko (opsional)
     */
    public function updateStatus(Request $request)
    {
        \DB::table('toko_status')->updateOrInsert(
            ['id' => 1],
            [
                'status' => $request->status,
                'waktu' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Status toko berhasil diperbarui',
            'status' => $request->status,
            'statusTime' => now()->format('d/m/Y H:i'),
        ]);
    }
}
