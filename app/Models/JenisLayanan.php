<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisLayanan extends Model
{
    use HasFactory;

    protected $table = 'jenis_layanan';
    protected $fillable = [
        'mitra_id',
        'nama_layanan',
        'deskripsi',
        'satuan',
        'harga'
        // 'harga_per_kg'
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'jenis_layanan_id');
    }
}
