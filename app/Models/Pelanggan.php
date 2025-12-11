<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

class Pelanggan extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'alamat',
        'kecamatan_id',
    ];

   protected $table = 'pelanggan';
//    protected $table = 'pengiriman';

// public function user()
// {
//     return $this->belongsTo(User::class);
// }  

public function user()
{
    return $this->belongsTo(User::class);
}


public function order()
{
    return $this->hasMany(Order::class);
}

public function kecamatan()
{
    return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
}



}