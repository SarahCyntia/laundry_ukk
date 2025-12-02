<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'order_id',
        'user_id',
        'mitra_id',
        'berat',
        'harga_final',
        'midtrans_order_id',
        'snap_token',
        'payment_type',
        'status_transaksi',
        'payment_code',
        'pdf_url',
    ];

    // RELASI ORDER
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // RELASI USER (Pelanggan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // RELASI MITRA
    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }
}



// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use Spatie\Permission\Traits\HasRoles;

// class Transaksi extends Model
// {
//      use HasRoles;

//      protected $table = 'transaksi';

//     protected $fillable = [
//         'user_id',
//         'mitra_id',
//         'berat',
//         'total_harga',
//         'status'
//     ];

//     public function mitra()
//     {
//         return $this->belongsTo(Mitra::class);
//     }

//     public function user()
//     {
//         return $this->belongsTo(User::class);
//     }
// }
