<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'transaksi';
    protected $primaryKey = 'id';
    public $incrementing = "true";
    public $timestamps = "true";
    protected $fillable = [
        'nota_layanan',
        'nota_pelanggan',
        'waktu',
        'total_biaya_layanan',
        'total_biaya_prioritas',
        'total_biaya_layanan_tambahan',
        'total_bayar_akhir',
        'jenis_pembayaran',
        'bayar',
        'kembalian',
        'status',
        'layanan_prioritas_id',
        'pelanggan_id',
        'pegawai_id',
    ];

    // public function detailTransaksi()
    // {
    //     return $this->hasMany(DetailTransaksi::class);
    // }

    public function layananTambahanTransaksi()
    {
        return $this->hasMany(LayananTambahanTransaksi::class);
    }

    // public function layananPrioritas()
    // {
    //     return $this->belongsTo(LayananPrioritas::class);
    // }

    // public function datapelanggan()
    // {
    //     return $this->belongsTo(dataPelanggan::class);
    // }

    // public function pegawai()
    // {
    //     return $this->belongsTo(User::class, 'pegawai_id');
    // }

    public function mitra()
{
    return $this->belongsTo(Mitra::class);
}
public function dataPelanggan()
{
    return $this->belongsTo(DataPelanggan::class, 'pelanggan_id');
}

public function pegawai()
{
    return $this->belongsTo(PegawaiLaundry::class, 'pegawai_id');
}

public function layananPrioritas()
{
    return $this->belongsTo(LayananPrioritas::class, 'layanan_prioritas_id');
}

public function detailTransaksi()
{
    return $this->hasMany(DetailTransaksi::class, 'transaksi_id');
}




}
