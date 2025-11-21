<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Resi Transaksi</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; }
        .title { font-size: 18px; font-weight: bold; text-align:center; margin-bottom: 10px; }
        .section { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top:5px; }
        td { padding: 4px; border-bottom: 1px solid #ccc; }
    </style>
</head>
<body>

    <div class="title">RESI TRANSAKSI LAUNDRY</div>

    <div class="section">
        <strong>ID Transaksi:</strong> {{ $transaksi->id }} <br>
        <strong>Tanggal:</strong> {{ $transaksi->created_at->format('d M Y H:i') }}
    </div>

    <div class="section">
        <strong>Data Pelanggan</strong>
        <table>
            <tr><td>Nama</td><td>{{ $transaksi->pelanggan->nama }}</td></tr>
            <tr><td>Alamat</td><td>{{ $transaksi->alamat_jemput }}</td></tr>
        </table>
    </div>

    <div class="section">
        <strong>Data Mitra</strong>
        <table>
            <tr><td>Nama Laundry</td><td>{{ $transaksi->mitra->nama_laundry }}</td></tr>
            <tr><td>Alamat</td><td>{{ $transaksi->mitra->alamat_laundry }}</td></tr>
        </table>
    </div>

    <div class="section">
        <strong>Detail Layanan</strong>
        <table>
            <tr><td>Jenis Layanan</td><td>{{ $transaksi->layanan->nama_layanan }}</td></tr>
            <tr><td>Harga / Kg</td><td>Rp {{ number_format($transaksi->layanan->harga_per_kg) }}</td></tr>
            <tr><td>Berat</td><td>{{ $transaksi->berat }} Kg</td></tr>
            <tr><td>Total Harga</td><td><strong>Rp {{ number_format($transaksi->total_harga) }}</strong></td></tr>
        </table>
    </div>

    <p style="text-align:center; margin-top:20px;">Terima kasih telah menggunakan layanan kami.</p>

</body>
</html>
