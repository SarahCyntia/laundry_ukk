<?php

namespace App\Http\Controllers;

use App\Models\JenisLayanan;
use App\Models\Mitra;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Dotenv\Util\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Midtrans\Snap;
use Midtrans\Config;


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

    if ($request->filter_type === 'weekly') {
        $query->whereDate('waktu_pelanggan_antar', '>=', $request->start_date)
      ->whereDate('waktu_pelanggan_antar', '<=', $request->end_date);

        // $query->whereBetween('waktu_pelanggan_antar', [
        //     $request->start_date . ' 00:00:00',
        //     $request->end_date . ' 23:59:59'
        // ]);
    }

    if ($request->filter_type === 'monthly') {
        $query->whereYear('waktu_pelanggan_antar', substr($request->month, 0, 4))
              ->whereMonth('waktu_pelanggan_antar', substr($request->month, 5, 2));
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

                'created_at' => $o->created_at->format('Y-m-d H:i'),
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
        $user = auth()->user();
        $mitra = Mitra::where('user_id', $user->id)->first();

        $query = Order::where('mitra_id', $mitra->id);

        // 🔥 FILTER SAMA PERSIS DENGAN TABEL
        if ($request->filter_date) {
            $query->whereDate('waktu_pelanggan_antar', $request->filter_date);
        }

        if ($request->filter_start_date && $request->filter_end_date) {
            $query->whereBetween('waktu_pelanggan_antar', [
                $request->filter_start_date,
                $request->filter_end_date
            ]);
        }

        if ($request->filter_month) {
            $query->whereYear('waktu_pelanggan_antar', substr($request->filter_month, 0, 4))
                  ->whereMonth('waktu_pelanggan_antar', substr($request->filter_month, 5, 2));
        }

        $orders = $query->with(['pelanggan.user', 'mitra', 'jenis_layanan', 'transaksi'])->get();

        $pdf = Pdf::loadView('pdf.laporan-order', compact('orders'));

        return $pdf->download('laporan-order.pdf');
    }


  
}