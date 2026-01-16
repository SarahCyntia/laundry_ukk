<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Laundry</title>
   <style>
* {
    box-sizing: border-box;
}

body {
    font-family: "Courier New", monospace;
    font-size: 9px;
    line-height: 1.3;
    width: 58mm;
    margin: 0;
    padding: 3mm;
}

.center {
    text-align: center;
}

.bold {
    font-weight: bold;
}

.hr {
    border-top: 1px dashed #000;
    margin: 5px 0;
}

/* ROW STRUK */
.item {
    display: block;
    width: 100%;
    clear: both;
}

.label {
    display: inline-block;
    width: 20mm;     /* KUNCI LEBAR LABEL */
}

.separator {
    display: inline-block;
    width: 3mm;
}

.value {
    display: inline-block;
    width: 30mm;     /* SISA LEBAR */
    vertical-align: top;
    word-wrap: break-word;
}

@page {
    size: 58mm auto;
    margin: 0;
}
</style>

</head>
<body>
<div class="center bold">
========================<br>
STRUK LAUNDRY<br>
{{ $data->mitra->nama ?? 'Laundry Bersih Jaya' }}<br>
========================
</div>

<br>

<div class="item">
    <span class="label">Kode Order</span>
    <span class="separator">:</span>
    <span class="value">{{ $data->kode_order }}</span>
</div>

<div class="item">
    <span class="label">Tanggal</span>
    <span class="separator">:</span>
    <span class="value">{{ $data->created_at->format('d-m-Y H:i') }}</span>
</div>

<br>

<div class="item">
    <span class="label">Pelanggan</span>
    <span class="separator">:</span>
    <span class="value">{{ $data->pelanggan->nama }}</span>
</div>

<div class="item">
    <span class="label">No HP</span>
    <span class="separator">:</span>
    <span class="value">{{ $data->pelanggan->no_hp ?? '-' }}</span>
</div>

<br>

<div class="item">
    <span class="label">Layanan</span>
    <span class="separator">:</span>
    <span class="value">{{ $data->jenisLayanan->nama ?? '-' }}</span>
</div>

<div class="item">
    <span class="label">Berat</span>
    <span class="separator">:</span>
    <span class="value">{{ number_format($data->berat_aktual, 1) }} Kg</span>
</div>

<div class="item">
    <span class="label">Harga</span>
    <span class="separator">:</span>
    <span class="value">Rp {{ number_format($data->harga_final, 0, ',', '.') }}</span>
</div>

<div class="hr"></div>

<div class="item bold">
    <span class="label">TOTAL</span>
    <span class="separator">:</span>
    <span class="value">Rp {{ number_format($data->harga_final, 0, ',', '.') }}</span>
</div>

<div class="item">
    <span class="label">Status Bayar</span>
    <span class="separator">:</span>
    <span class="value">{{ strtoupper($data->status_pembayaran) }}</span>
</div>

<div class="hr"></div>

<br>

<div class="center">
Terima kasih 🙏
</div>

</body>
</html>
