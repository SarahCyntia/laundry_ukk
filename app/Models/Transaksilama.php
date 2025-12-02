<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiLama extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'kode_transaksi',
        'pelanggan_id',
        'mitra_id',
        'kurir_pickup_id',
        'kurir_antar_id',
        'layanan_id',
        'alamat_pickup',
        'alamat_dropoff',
        'berat',
        'harga',
        'biaya_pickup',
        'biaya_antar',
        'total',
        'catatan',
        'metode_pembayaran',
        'bukti_pembayaran',
        'status',
        'tanggal_order',
        'tanggal_pickup',
        'tanggal_selesai',
        'tanggal_antar',
    ];

    protected $dates = [
        'tanggal_order',
        'tanggal_pickup',
        'tanggal_selesai',
        'tanggal_antar',
        'created_at',
        'updated_at',
    ];

    /**
     * Relasi ke pelanggan (user)
     */
    public function pelanggan()
    {
        return $this->belongsTo(User::class, 'pelanggan_id');
    }

    /**
     * Relasi ke mitra laundry
     */
    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    /**
     * Relasi ke kurir pickup
     */
    public function kurirPickup()
    {
        return $this->belongsTo(Kurir::class, 'kurir_pickup_id');
    }

    /**
     * Relasi ke kurir antar
     */
    public function kurirAntar()
    {
        return $this->belongsTo(Kurir::class, 'kurir_antar_id');
    }

    /**
     * Relasi ke jenis layanan
     */
    public function layanan()
    {
        return $this->belongsTo(JenisLayanan::class, 'layanan_id');
    }

    /**
     * Accessor untuk status readable
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'menunggu_kurir' => 'Menunggu Kurir',
            'pickup' => 'Diambil Kurir',
            'dicuci' => 'Sedang Dicuci',
            'selesai_cuci' => 'Cuci Selesai',
            'antar' => 'Sedang Diantar',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }
}
