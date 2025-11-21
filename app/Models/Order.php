<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Jika nama tabel bukan "orders", aktifkan ini:
    // protected $table = 'orders';
protected $table = 'order';
    protected $fillable = [
        'pelanggan_id',
        'mitra_id',
        'jenis_layanan_id',
        'kode_order',
        'berat_estimasi',
        'berat_aktual',
        'harga_final',
        'catatan',
        'status',
        'alasan_penolakan',
        'waktu_pelanggan_antar',
        'waktu_diambil',
    ];

    protected $casts = [
        'waktu_pelanggan_antar' => 'datetime',
        'waktu_diambil' => 'datetime',
    ];

    /* ================================
       RELASI
    ================================= */

    // Order dimiliki oleh 1 pelanggan
    // public function pelanggan()
    // {
    //     return $this->belongsTo(Pelanggan::class);
    // }

    // Order dimiliki oleh 1 mitra (laundry)
    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }
    public function pelanggan()
{
    return $this->belongsTo(User::class, 'pelanggan_id');
}


public function jenis_layanan()
{
    return $this->belongsTo(JenisLayanan::class, 'jenis_layanan_id');
}

}
