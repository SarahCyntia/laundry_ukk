<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payment';

    protected $fillable = [
        'order_id',
        'status',
        'price',
        'item_name',
        'customer',
        'customer_email',
        'checkout_link',
    ];

    public $timestamps = true;

    protected $casts = [
        'price' => 'double',
    ];
}