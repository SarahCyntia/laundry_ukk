<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable; // biar bisa login
use Illuminate\Notifications\Notifiable;

class Mitra extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

   protected $table = 'mitra';

    protected $fillable = [
        'user_id',
        'nama_laundry',
        'status_validasi',
        'kecamatan_id',
        'alamat_laundry',
        'foto_ktp',
        'status_toko',
        'wilayah_id',
        'jam_buka',
        'jam_tutup',
        'foto_toko',
        'deskripsi',
        'estimasi_selesai',
        'estimasi_jam'

    ];

    protected $hidden = [
        'password',
    ];

    // ✅ Relasi ke tabel lain

    // public function transaksi()
    // {
    //     return $this->hasMany(Transaksi::class, 'mitra_id');
    // }


    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    public function pegawai()
    {
        return $this->hasMany(PegawaiLaundry::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jenis_layanan()
    {
        return $this->hasMany(JenisLayanan::class); // asumsi 1 mitra bisa punya banyak layanan
    }

    public function order()
    {
        return $this->hasMany(Order::class, 'mitra_id');
    }

      public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }
}
