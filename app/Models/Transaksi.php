<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'order_id',
        'total_bayar',
        // 'biaya_admin',
        // 'diskon',
        'status_pembayaran',
        'metode_pembayaran',
        'payment_reference',
        'waktu_bayar',
    ];

    protected $casts = [
        'waktu_bayar' => 'datetime',
    ];

    /* ================= RELATION ================= */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /* ================= QUERY SCOPE ================= */

    public function scopeSettlement($query)
    {
        return $query->where('status_pembayaran', 'settlement');
    }

    public function scopeMitra($query, $mitraId)
    {
        return $query->whereHas('order', function ($q) use ($mitraId) {
            $q->where('mitra_id', $mitraId);
        });
    }
}
