<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Mitra;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;
class OrderExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;
    protected $no = 0;

   public function __construct(Request $request)
{
    $this->request = $request;
}

    /**
     * Ambil data sesuai filter
     */

public function collection(): Collection
{
    $filterType = $this->request->filter_type;

    // 🔥 AMBIL USER LOGIN
    $user = auth()->user();

    // 🔥 AMBIL MITRA DARI USER
    $mitra = Mitra::where('user_id', $user->id)->first();

    if (!$mitra) {
        return collect(); // kosong aman
    }

    // 🔒 KUNCI DATA PER MITRA
    $query = Order::with(['pelanggan.user', 'mitra', 'jenis_layanan', 'transaksi'])
        ->where('mitra_id', $mitra->id);

    // ================= FILTER =================
    if ($filterType === 'daily' && $this->request->filled('date')) {
        $query->whereDate(
            'created_at',
            Carbon::parse($this->request->date)
        );
    }

    if ($filterType === 'weekly' && $this->request->filled(['start_date','end_date'])) {
        $query->whereBetween('created_at', [
            $this->request->start_date,
            $this->request->end_date
        ]);
    }

    if ($filterType === 'monthly' && $this->request->filled('month')) {
        [$year, $month] = explode('-', $this->request->month);

        $query->whereYear('created_at', $year)
              ->whereMonth('created_at', $month);
    }

    return $query->orderBy('created_at')->get();
}


    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'No',
            'Kode Order',
            'Nama Pelanggan',
            'Nama Laundry',
            'Layanan',
            'Berat (Kg)',
            'Harga',
            'Status Order',
            'Status Pembayaran',
            'Tanggal Order',
        ];
    }

    /**
     * Mapping tiap baris
     */
    public function map($order): array
    {
        return [
            ++$this->no,
            $order->kode_order,
            $order->pelanggan->user->name ?? '-',
            $order->mitra->nama_laundry ?? '-',
            $order->jenis_layanan->nama_layanan ?? '-',
            $order->berat_aktual ?? $order->berat_estimasi,
            $order->harga_final,
            ucwords(str_replace('_', ' ', $order->status)),
            ucwords(str_replace('_', ' ', $order->transaksi->status_pembayaran ?? 'belum')),
            $order->created_at->format('d-m-Y'),
        ];
    }
}
