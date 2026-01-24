<?php

namespace App\Exports;
use App\Models\Order;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanKeuanganExport implements FromCollection, WithHeadings
{
    public function __construct($order)
    {
        $this->order = $order;
    }

    public function collection()
    {
        return $this->order->map(fn ($o) => [
            $o->kode_order,
            $o->pelanggan->user->name ?? '-',
            $o->mitra->nama_laundry,
            $o->status,
            $o->transaksi->total_bayar ?? 0,
            $o->transaksi->status_pembayaran ?? '-',
            $o->transaksi->waktu_bayar,
        ]);
    }

    public function headings(): array
    {
        return [
            'No Order',
            'Pelanggan',
            'Laundry',
            'Status',
            'Total Bayar',
            'Status Pembayaran',
            'Waktu Bayar'
        ];
    }
}

