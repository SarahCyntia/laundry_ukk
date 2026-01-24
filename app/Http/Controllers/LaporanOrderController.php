<?php

namespace App\Http\Controllers;

use App\Models\JenisLayanan;
use App\Models\Mitra;
use App\Models\Order;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Dotenv\Util\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;



class LaporanOrderController extends Controller
{

    public function index(Request $request)
    {
        $per = $request->per ?? 10;

        $user = auth()->user();
        $mitra = Mitra::where('user_id', $user->id)->first();

        if (!$mitra) {
            return response()->json(['message' => 'Mitra tidak ditemukan'], 404);
        }

        // 🔥 QUERY UTAMA
        $query = Order::where('mitra_id', $mitra->id)
            ->with([
                'pelanggan.user:id,name',
                'jenis_layanan:id,nama_layanan',
                'mitra:id,user_id,nama_laundry',
                'transaksi:id,order_id,status_pembayaran,metode_pembayaran,waktu_bayar'
            ]);

        // 🔹 STATUS FILTER
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('exclude_status')) {
            $query->whereNotIn('status', $request->exclude_status);
        }

        // 🔥 FILTER LAPORAN
        if ($request->filter_type === 'daily') {
            $query->whereDate('waktu_pelanggan_antar', $request->date);
        }

        if (
            $request->filter_type === 'weekly' &&
            $request->filled(['start_date', 'end_date'])
        ) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();

            $query->whereBetween('waktu_pelanggan_antar', [$start, $end]);
        }

        if ($request->filter_type === 'monthly') {

            // frontend kirim: YYYY-MM
            [$year, $month] = explode('-', $request->month);

            $query->whereYear('waktu_pelanggan_antar', (int) $year)
                ->whereMonth('waktu_pelanggan_antar', (int) $month);

            $periodTitle = 'Laporan Bulanan - ' . Carbon::create($year, $month)->translatedFormat('F Y');
        }

        // 🔹 PAGINATE SETELAH FILTER
        $data = $query
            ->orderBy('waktu_pelanggan_antar', 'desc')
            ->paginate($per);

        // 🔹 TRANSFORM DATA (AMAN)
        $start = $data->firstItem();
        $data->getCollection()->transform(function ($o) use (&$start) {
            return [
                'no' => $start++,
                'id' => $o->id,
                'kode_order' => $o->kode_order,

                'pelanggan' => [
                    'id' => $o->pelanggan->id ?? null,
                    'name' => $o->pelanggan->user->name ?? "-",
                ],

                'mitra' => [
                    'id' => $o->mitra->id ?? null,
                    'nama_laundry' => $o->mitra->nama_laundry ?? "-",
                ],

                'jenis_layanan' => [
                    'id' => $o->jenis_layanan->id ?? null,
                    'nama_layanan' => $o->jenis_layanan->nama_layanan ?? "-",
                ],

                'status' => $o->status,
                'berat_estimasi' => $o->berat_estimasi,
                'berat_aktual' => $o->berat_aktual,
                'harga_final' => $o->harga_final,
                'alasan_penolakan' => $o->alasan_penolakan,
                'estimasi_selesai' => $o->estimasi_selesai,
                'estimasi_jam' => $o->estimasi_jam,
                'catatan' => $o->catatan,
                'foto_struk' => $o->foto_struk,

                // 🔥 INI YANG KURANG
                'waktu_pelanggan_antar' => $o->waktu_pelanggan_antar
                    ? $o->waktu_pelanggan_antar->format('Y-m-d H:i')
                    : null,

                'waktu' => $o->waktu->format('Y-m-d H:i'),
                'transaksi' => $o->transaksi ? [
                    'status_pembayaran' => $o->transaksi->status_pembayaran,
                    'metode_pembayaran' => $o->transaksi->metode_pembayaran,
                    'waktu_bayar' => $o->transaksi->waktu_bayar
                        ? $o->transaksi->waktu_bayar->format('Y-m-d H:i')
                        : null,
                ] : null,

            ];
        });

        // return response()->json($data);
        return response()->json($data);

        //   return response()->json([
//     'data' => $data
// ]);

    }


public function exportPdf(Request $request)
{
    $filterType = $request->filter_type;

    // 🔥 AMBIL USER LOGIN
    $user = auth()->user();

    // 🔥 AMBIL MITRA DARI USER
    $mitra = Mitra::where('user_id', $user->id)->first();

    if (!$mitra) {
        abort(403, 'Mitra tidak ditemukan');
    }

    // 🔒 QUERY DIKUNCI PER MITRA LOGIN
    $query = Order::with(['pelanggan.user','mitra','jenis_layanan','transaksi'])
        ->where('mitra_id', $mitra->id);

    // ================= FILTER PERIODE =================
    if ($filterType === 'daily' && $request->filled('date')) {
        $date = Carbon::parse($request->date);
        $query->whereDate('waktu', $date);
        $periodTitle = 'Laporan Harian - ' . $date->format('d M Y');



        } elseif ($filterType === 'weekly' && $request->filled(['start_date','end_date'])) {

    $start = Carbon::parse($request->start_date);
    $end   = Carbon::parse($request->end_date);

    $query->whereBetween('waktu', [
        $start->startOfDay(),
        $end->endOfDay()
    ]);

    $periodTitle = 'Laporan Mingguan (' 
        . $start->translatedFormat('d M Y') 
        . ' - ' 
        . $end->translatedFormat('d M Y') 
        . ')';



    // } elseif ($filterType === 'weekly' && $request->filled(['start_date','end_date'])) {
    //     $query->whereBetween('waktu', [
    //         $request->start_date,
    //         $request->end_date
    //     ]);
    //     $periodTitle = 'Laporan Mingguan';

    } elseif ($filterType === 'monthly' && $request->filled('month')) {
        [$year, $month] = explode('-', $request->month);
        $query->whereYear('waktu', $year)
              ->whereMonth('waktu', $month);

        $periodTitle = 'Laporan Bulanan - ' .
            Carbon::createFromDate($year, $month)->translatedFormat('F Y');
    } else {
        $periodTitle = 'Semua Data';
    }

    $order = $query->orderBy('waktu','desc')->get();

    return Pdf::loadView('reports.order-pdf', [
        'order' => $order,
        'periodTitle' => $periodTitle,
        'totalOrder' => $order->count(),
        'totalHarga' => $order->sum('harga_final'),
        'generatedAt' => now()->format('d M Y H:i')
    ])->download('laporan-order.pdf');
}




    //sebelumnya
    // public function exportPdf(Request $request)
    // {
    //     $filterType = $request->filter_type;

    //     $query = Order::with(['pelanggan', 'mitra', 'jenis_layanan', 'transaksi'])
    //     ->where('mitra_id', auth()->user()->mitra_id);



    //     if ($filterType === 'daily') {
    //         $date = Carbon::parse($request->date);
    //         $query->whereDate('waktu', $date);
    //         $periodTitle = 'Laporan Harian - ' . $date->format('d M Y');

    //     } elseif ($filterType === 'weekly') {
    //         $query->whereBetween('waktu', [
    //             $request->start_date,
    //             $request->end_date
    //         ]);
    //         $periodTitle = 'Laporan Mingguan (' .
    //             $request->start_date . ' s/d ' . $request->end_date . ')';

    //     } elseif ($filterType === 'monthly' && filled($request->month)) {

    //         try {
    //             [$year, $month] = explode('-', $request->month);
    //         } catch (\Throwable $e) {
    //             dd('FORMAT BULAN SALAH', $request->month);
    //         }

    //         $query->whereYear('waktu_pelanggan_antar', (int) $year)
    //             ->whereMonth('waktu_pelanggan_antar', (int) $month);

    //         $periodTitle = 'Laporan Bulanan - ' .
    //             Carbon::createFromDate($year, $month)->translatedFormat('F Y');
    //     }
    //     $order = $query->get();

    //     $totalOrder = $order->count();
    //     $totalHarga = $order->sum('harga_final');

    //     $pdf = Pdf::loadView('reports.order-pdf', [
    //         'order' => $order,
    //         'periodTitle' => $periodTitle,
    //         'totalOrder' => $totalOrder,
    //         'totalHarga' => $totalHarga,
    //         'generatedAt' => now()->format('d M Y H:i')
    //     ])->setPaper('A4', 'portrait');

    //     return $pdf->download('laporan-order.pdf');
    // }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new OrderExport($request),
            'laporan-order.xlsx'
        );
    }


    // public function exportPdf(Request $request)
//     {
//         $user = auth()->user();
//         $mitra = Mitra::where('user_id', $user->id)->first();

    //         $query = Order::where('mitra_id', $mitra->id);

    //         // 🔥 FILTER SAMA PERSIS DENGAN TABEL
//         if ($request->filter_date) {
//             $query->whereDate('waktu_pelanggan_antar', $request->filter_date);
//         }

    //         if ($request->filter_start_date && $request->filter_end_date) {
//             $query->whereBetween('waktu_pelanggan_antar', [
//                 $request->filter_start_date,
//                 $request->filter_end_date
//             ]);
//         }

    //         if ($request->filter_month) {
//             $query->whereYear('waktu_pelanggan_antar', substr($request->filter_month, 0, 4))
//                   ->whereMonth('waktu_pelanggan_antar', substr($request->filter_month, 5, 2));
//         }

    //         $order = $query->with(['pelanggan.user', 'mitra', 'jenis_layanan', 'transaksi'])->get();

    //         $pdf = Pdf::loadView('reports.order-pdf', compact('order'));

    //         return $pdf->download('laporan-order.pdf');
//     }



}