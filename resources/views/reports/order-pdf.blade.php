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
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2c3e50;
        }
        
        .header h1 {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14px;
            color: #7f8c8d;
            font-weight: normal;
        }
        
        .info-section {
            margin: 15px 0;
            padding: 10px;
            background: #ecf0f1;
            border-radius: 5px;
        }
        
        .info-section p {
            margin: 3px 0;
        }
        
        .info-section strong {
            color: #2c3e50;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        thead {
            background: #34495e;
            color: white;
        }
        
        thead th {
            padding: 10px 5px;
            text-align: left;
            font-size: 10px;
            font-weight: 600;
            border: 1px solid #2c3e50;
        }
        
        tbody td {
            padding: 8px 5px;
            border: 1px solid #bdc3c7;
            font-size: 10px;
        }
        
        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        tbody tr:hover {
            background: #e8f4f8;
        }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
            text-align: center;
        }
        
        .status-diterima { background: #27ae60; color: white; }
        .status-ditolak { background: #e74c3c; color: white; }
        .status-diproses { background: #f39c12; color: white; }
        .status-selesai { background: #3498db; color: white; }
        .status-menunggu { background: #95a5a6; color: white; }
        
        .payment-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
        }
        
        .payment-dibayar { background: #27ae60; color: white; }
        .payment-menunggu { background: #f39c12; color: white; }
        .payment-belum { background: #95a5a6; color: white; }
        
        .summary {
            margin-top: 20px;
            padding: 15px;
            background: #e8f4f8;
            border-left: 4px solid #3498db;
        }
        
        .summary h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 12px;
        }
        
        .summary-row strong {
            color: #2c3e50;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #bdc3c7;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
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
                <th width="12%">Kode Order</th>
                <th width="15%">Pelanggan</th>
                <th width="15%">Laundry</th>
                <th width="12%">Layanan</th>
                <th width="8%">Berat (kg)</th>
                <th width="10%">Harga</th>
                <th width="12%">Status</th>
                <th width="13%">Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $order->kode_order }}</td>
                <td>{{ $order->pelanggan->name ?? '-' }}</td>
                <td>{{ $order->mitra->nama_laundry ?? '-' }}</td>
                <td>{{ $order->jenis_layanan->nama_layanan ?? '-' }}</td>
                <td class="text-center">
                    {{ $order->berat_aktual ?? $order->berat_estimasi }}
                </td>
                <td class="text-right">
                    Rp {{ number_format($order->harga_final, 0, ',', '.') }}
                </td>
                <td>
                    @php
                        $statusClass = 'status-menunggu';
                        $statusText = ucwords(str_replace('_', ' ', $order->status));
                        
                        if (str_contains($order->status, 'diterima')) $statusClass = 'status-diterima';
                        elseif (str_contains($order->status, 'ditolak')) $statusClass = 'status-ditolak';
                        elseif (str_contains($order->status, 'diproses') || str_contains($order->status, 'dicuci')) $statusClass = 'status-diproses';
                        elseif (str_contains($order->status, 'selesai')) $statusClass = 'status-selesai';
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                </td>
                <td>
                    @php
                        $paymentStatus = $order->transaksi->status_pembayaran ?? 'belum_dibayar';
                        $paymentClass = 'payment-belum';
                        $paymentText = ucwords(str_replace('_', ' ', $paymentStatus));
                        
                        if ($paymentStatus === 'dibayar') $paymentClass = 'payment-dibayar';
                        elseif ($paymentStatus === 'menunggu_pembayaran') $paymentClass = 'payment-menunggu';
                    @endphp
                    <span class="payment-badge {{ $paymentClass }}">{{ $paymentText }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 20px; color: #7f8c8d;">
                    Tidak ada data order untuk periode ini
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <h3>RINGKASAN LAPORAN</h3>
        <div class="summary-row">
            <span>Jumlah Total Pesanan:</span>
            <strong>{{ $totalOrder }} Pesanan</strong>
        </div>
        <div class="summary-row">
            <span>Total Pendapatan:</span>
            <strong>Rp {{ number_format($totalHarga, 0, ',', '.') }}</strong>
        </div>
        <div class="summary-row">
            <span>Rata-rata per Pesanan:</span>
            <strong>Rp {{ $totalOrder > 0 ? number_format($totalHarga / $totalOrder, 0, ',', '.') : 0 }}</strong>
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh sistem.</p>
        <p>&copy; {{ date('Y') }} Sistem Laundry Management. All rights reserved.</p>
    </div>
</body>
</html>a