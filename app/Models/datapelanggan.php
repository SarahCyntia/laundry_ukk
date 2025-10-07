<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class datapelanggan extends Model
{
    use HasFactory;

    protected $table = 'data_pelanggan';

    protected $fillable = [
        'nama',
        'jenis_kelamin',
        'telepon',
        'alamat',
    ];
}
