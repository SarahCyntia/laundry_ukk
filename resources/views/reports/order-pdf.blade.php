<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Order Laundry</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #2c3e50;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 3px;
        }

        .header h2 {
            font-size: 13px;
            font-weight: normal;
            color: #666;
        }

        .info-section {
            margin: 8px 0;
            padding: 8px;
            background: #ecf0f1;
            border-radius: 4px;
            page-break-inside: avoid;
        }

        .info-section p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            page-break-inside: auto;
        }

        thead {
            background: #34495e;
            color: #fff;
        }

        thead th {
            padding: 6px 4px;
            font-size: 10px;
            border: 1px solid #2c3e50;
        }

        tbody td {
            padding: 5px 4px;
            font-size: 10px;
            border: 1px solid #bdc3c7;
        }

        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .status-badge,
        .payment-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            color: #fff;
            display: inline-block;
        }

        .status-diterima { background: #27ae60; }
        .status-ditolak { background: #e74c3c; }
        .status-diproses { background: #f39c12; }
        .status-selesai { background: #3498db; }
        .status-menunggu { background: #95a5a6; }

        .payment-dibayar { background: #27ae60; }
        .payment-menunggu { background: #f39c12; }
        .payment-belum { background: #95a5a6; }

        .summary {
            margin-top: 10px;
            padding: 8px;
            background: #e8f4f8;
            border-left: 3px solid #3498db;
            page-break-inside: avoid;
        }

        .summary h3 {
            font-size: 13px;
            margin-bottom: 6px;
        }

        .summary table td {
            padding: 3px 0;
            font-size: 11px;
        }

        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 9px;
            color: #777;
            page-break-inside: avoid;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

<div class="header">
    <h1>LAPORAN ORDER LAUNDRY</h1>
    <h2>Periode: {{ $periodTitle }}</h2>
</div>

<div class="info-section">
    <p><strong>Total Order:</strong> {{ $totalOrder }} Pesanan</p>
    <p><strong>Total Pendapatan:</strong> Rp {{ number_format($totalHarga, 0, ',', '.') }}</p>
    <p><strong>Tanggal Cetak:</strong> {{ $generatedAt }}</p>
</div>

<table>
    <thead>
        <tr>
            <th width="3%">No</th>
            <th width="12%">Kode</th>
            <th width="15%">Pelanggan</th>
            <th width="15%">Laundry</th>
            <th width="12%">Layanan</th>
            <th width="8%">Berat</th>
            <th width="10%">Harga</th>
            <th width="12%">Status</th>
            <th width="13%">Bayar</th>
        </tr>
    </thead>
    <tbody>
        @forelse($order as $i => $item)
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td>{{ $item->kode_order }}</td>
            <td>{{ $item->pelanggan->user->name ?? '-' }}</td>
            <td>{{ $item->mitra->nama_laundry ?? '-' }}</td>
            <td>{{ $item->jenis_layanan->nama_layanan ?? '-' }}</td>
            <td class="text-center">{{ $item->berat_aktual ?? $item->berat_estimasi }}</td>
            <td class="text-right">Rp {{ number_format($item->harga_final, 0, ',', '.') }}</td>
            <td class="text-center">
                <span class="status-badge status-{{ str_contains($item->status,'selesai')?'selesai':'menunggu' }}">
                    {{ ucwords(str_replace('_',' ',$item->status)) }}
                </span>
            </td>
            <td class="text-center">
                <span class="payment-badge payment-{{ $item->transaksi->status_pembayaran ?? 'belum' }}">
                    {{ ucwords(str_replace('_',' ',$item->transaksi->status_pembayaran ?? 'belum')) }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="text-center">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="summary">
    <h3>Ringkasan</h3>
    <table width="100%">
        <tr>
            <td>Total Pesanan</td>
            <td class="text-right"><strong>{{ $totalOrder }}</strong></td>
        </tr>
        <tr>
            <td>Total Pendapatan</td>
            <td class="text-right"><strong>Rp {{ number_format($totalHarga, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td>Rata-rata</td>
            <td class="text-right">
                <strong>Rp {{ $totalOrder ? number_format($totalHarga/$totalOrder,0,',','.') : 0 }}</strong>
            </td>
        </tr>
    </table>
</div>

<div class="footer">
    Dokumen ini digenerate otomatis • {{ date('Y') }}
</div>

</body>
</html>
