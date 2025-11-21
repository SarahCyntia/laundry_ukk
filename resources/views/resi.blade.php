<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resi Laundry #{{ $transaksi->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 10px; }
        .section h4 { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #333; }
        th, td { padding: 8px; text-align: left; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Resi Laundry</h2>
        <p>#{{ $transaksi->id }}</p>
    </div>

    <div class="section">
        <h4>Data Pelanggan</h4>
        <p>Nama: {{ $transaksi->pelanggan->nama }}</p>
        <p>Alamat: {{ $transaksi->alamat_jemput }}</p>
    </div>

    <div class="section">
        <h4>Data Mitra Laundry</h4>
        <p>Nama: {{ $transaksi->mitra->nama_laundry }}</p>
        <p>Alamat: {{ $transaksi->mitra->alamat_laundry }}</p>
    </div>

    <div class="section">
        <h4>Detail Transaksi</h4>
        <table>
            <thead>
                <tr>
                    <th>Layanan</th>
                    <th>Berat (Kg)</th>
                    <th>Harga per Kg</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $transaksi->layanan->nama_layanan }}</td>
                    <td>{{ $transaksi->berat }}</td>
                    <td>Rp {{ $transaksi->layanan->harga_per_kg }}</td>
                    <td>Rp {{ $transaksi->total_harga }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h4>Status: {{ ucfirst($transaksi->status) }}</h4>
    </div>

    <div class="section" style="margin-top:30px; text-align:center;">
        <p>Terima kasih telah menggunakan layanan laundry kami.</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
