<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

// Models (sesuaikan dengan punyamu)
use App\Models\User;
use App\Models\Mitra;
use App\Models\Order;
use App\Models\Komplain;
use App\Models\Laporan;
use App\Models\Pelanggan;


class DashboardAdminController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');

        // ===============================
        // Filter tanggal
        // ===============================
        $startDate = match ($period) {
            'week'  => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            default => Carbon::today(),
        };

        // ===============================
        // TRANSAKSI
        // ===============================
        $transaksiQuery = Order::where('created_at', '>=', $startDate);

        $totalTransaksi = (clone $transaksiQuery)->count();
        $totalPendapatan = (clone $transaksiQuery)
            ->where('status_pembayaran', 'settlement')
            ->sum('harga_final');

        $rataRataTransaksi = $totalTransaksi > 0
            ? $totalPendapatan / $totalTransaksi
            : 0;

        // Status transaksi
        $transaksiMenunggu = (clone $transaksiQuery)->where('status', 'menunggu')->count();
        $transaksiProses   = (clone $transaksiQuery)->where('status', 'proses')->count();
        $transaksiSelesai  = (clone $transaksiQuery)->where('status', 'selesai')->count();

        // ===============================
        // OVERVIEW HARI INI
        // ===============================
        $totalTransaksiHariIni = Order::whereDate('created_at', today())->count();
        $totalPendapatanHariIni = Order::whereDate('created_at', today())
            ->where('status_pembayaran', 'settlement')
            ->sum('harga_final');

        // ===============================
        // MITRA
        // ===============================
        $totalMitra   = Mitra::count();
        $mitraAktif   = Mitra::where('status_toko', 'buka')->count();
        $mitraTutup   = Mitra::where('status_toko', 'tutup')->count();
        $mitraPending = Mitra::where('status_validasi', 'pending')->count();

        // ===============================
        // USER / PELANGGAN
        // ===============================
        $totalPelanggan = Pelanggan::count();
        $pelangganAktif = Pelanggan::whereHas('order', function ($q) {
    $q->whereMonth('created_at', now()->month);
})->count();

       $penggunaBaru = Pelanggan::whereDate('created_at', today())->count();
       $totalAdmin = User::role('admin')->count();

        // ===============================
        // KOMPLAIN & LAPORAN
        // ===============================
        $komplainBelumDitangani = Komplain::where('status', 'pending')->count();
        $laporanBelumDitinjau  = Laporan::where('status', 'pending')->count();

        // ===============================
        // RESPONSE
        // ===============================
        return response()->json([
            // Overview
            'totalTransaksiHariIni'   => $totalTransaksiHariIni,
            'totalPendapatanHariIni'  => $totalPendapatanHariIni,
            'totalMitra'              => $totalMitra,
            'totalPelanggan'          => $totalPelanggan,

            // Mitra
            'mitraAktif'   => $mitraAktif,
            'mitraTutup'   => $mitraTutup,
            'mitraPending' => $mitraPending,

            // User
            'pelangganAktif' => $pelangganAktif,
            'penggunaBaru'   => $penggunaBaru,
            'totalAdmin'    => $totalAdmin,

            // Transaksi
            'totalTransaksi' => $totalTransaksi,
            'totalPendapatanFormatted' => $this->rupiah($totalPendapatan),
            'rataRataTransaksiFormatted' => $this->rupiah($rataRataTransaksi),

            'transaksiMenunggu' => $transaksiMenunggu,
            'transaksiProses'   => $transaksiProses,
            'transaksiSelesai'  => $transaksiSelesai,

            // Alerts
            'komplainBelumDitangani' => $komplainBelumDitangani,
            'laporanBelumDitinjau'   => $laporanBelumDitinjau,
        ]);
    }

    private function rupiah($value)
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}
