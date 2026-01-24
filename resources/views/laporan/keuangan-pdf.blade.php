<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Order Laundry</title>
    <style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 11px;
        color: #333;
    }

    .header {
        text-align: center;
        margin-bottom: 10px;
        border-bottom: 2px solid #000;
        padding-bottom: 6px;
    }

    .header h1 {
        font-size: 16px;
        margin: 0;
    }

    .header h2 {
        font-size: 12px;
        margin: 0;
        font-weight: normal;
    }

    .info-section {
        margin: 10px 0;
        font-size: 11px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    table thead {
        background: #f2f2f2;
    }

    table th,
    table td {
        border: 1px solid #000;
        padding: 4px;
        font-size: 10px;
        text-align: center;
    }

    table th {
        font-weight: bold;
    }

    .text-left {
        text-align: left;
    }

    .text-right {
        text-align: right;
    }

    .footer {
        margin-top: 15px;
        font-size: 9px;
        text-align: center;
        color: #666;
    }
</style>

</head>
<body>

<div class="header">
    <h1>LAPORAN ORDER LAUNDRY </h1>
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
            <th>No</th>
            <th>Kode</th>
            <th>Pelanggan</th>
            <th>Laundry</th>
            <th>Layanan</th>
            <th>Berat</th>
            <th>Harga</th>
            <th>Status</th>
            <th>Status Pembayaran</th>
        </tr>
    </thead>
    <tbody>
        @forelse($order as $i => $item)
        <tr>
            



            <td>{{ $i + 1 }}</td>
            <td>{{ $item->kode_order }}</td>
            <td>{{ $item->pelanggan->user->name ?? '-' }}</td>
            <td>{{ $item->mitra->nama_laundry ?? '-' }}</td>
            <td>{{ $item->jenis_layanan->nama_layanan ?? '-' }}</td>
            <td>{{ $item->berat_aktual ?? $item->berat_estimasi }}</td>
            <td>Rp {{ number_format($item->harga_final, 0, ',', '.') }}</td>
            <td>{{ strtoupper($item->status) }}</td>
            <td>{{ strtoupper($item->transaksi->status_pembayaran ?? 'belum') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" align="center">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    Dokumen ini digenerate otomatis • {{ date('Y') }}
</div>

</body>
</html>
