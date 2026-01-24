<?php

namespace App\Models;

use Carbon\Carbon;
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
        'snap_token',
    ];

    protected $casts = [
        'waktu_bayar' => 'datetime',
    ];

    /* ================= RELATION ================= */

    public function transaksi()
{
    return $this->hasMany(Transaksi::class, 'order_id');
}

    // public function order()
    // {
    //     return $this->belongsTo(Order::class);
    // }

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


    public function scopeToday($query)
    {
        return $query->whereDate('waktu_bayar', Carbon::today());
    }

    public function scopeYesterday($query)
    {
        return $query->whereDate('waktu_bayar', Carbon::yesterday());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('waktu_bayar', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    public function scopeLastWeek($query)
    {
        return $query->whereBetween('waktu_bayar', [
            Carbon::now()->subWeek()->startOfWeek(),
            Carbon::now()->subWeek()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('waktu_bayar', Carbon::now()->month)
                    ->whereYear('waktu_bayar', Carbon::now()->year);
    }

    public function scopeLastMonth($query)
    {
        return $query->whereMonth('waktu_bayar', Carbon::now()->subMonth()->month)
                    ->whereYear('waktu_bayar', Carbon::now()->subMonth()->year);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('waktu_bayar', [$startDate, $endDate]);
    }
}
