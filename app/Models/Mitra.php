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
        'nama_laundry',
        'status_validasi',
        'alamat_laundry',
        'foto_ktp',
        'status_toko',
        'user_id',
    ];

    protected $hidden = [
        'password',
    ];

    // ✅ Relasi ke tabel lain

    public function dataPelanggan()
    {
        return $this->hasMany(DataPelanggan::class);
    }

    public function pegawai()
    {
        return $this->hasMany(PegawaiLaundry::class);
    }

    public function jenisLayanan()
    {
        return $this->hasMany(JenisLayanan::class);
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function layananTambahan()
    {
        return $this->hasMany(LayananTambahan::class);
    }
    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

}
