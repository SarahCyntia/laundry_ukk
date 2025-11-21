<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiTracking extends Model
{
    protected $fillable = [
        'transaksi_id',
        'status',
        'keterangan',
        'kurir_id',
        'lokasi'
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function kurir()
    {
        return $this->belongsTo(Kurir::class);
    }
}
