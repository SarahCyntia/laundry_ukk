<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisItem extends Model
{
    use HasFactory, SoftDeletes;

    
    protected $table = 'jenis_item';
    protected $primaryKey = 'id';
    public $incrementing = "true";
    public $timestamps = "true";
    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    public function hargaJenisLayanan()
    {
        return $this->hasMany(HargaJenisLayanan::class);
    }
    
}
