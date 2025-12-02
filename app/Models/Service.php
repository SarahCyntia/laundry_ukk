<?php
// app/Models/Service.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'harga_per_kg',
        'durasi_jam'
    ];

    public function order()
    {
        return $this->hasMany(Order::class);
    }
}