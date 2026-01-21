<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('transaksi', function (Blueprint $table) {
    $table->id();

    // Relasi ke order
    $table->foreignId('order_id')->constrained('order')->cascadeOnDelete();

    // Keuangan
    $table->integer('total_bayar');
    // $table->integer('biaya_admin')->nullable();
    // $table->integer('diskon')->nullable();

    // Pembayaran
    $table->enum('status_pembayaran', [
        'belum_dibayar',
        'menunggu_pembayaran',  
        'dibayar',
        'kadaluarsa',
        'dibatalkan',
        'dikembalikan'
    ])->default('belum_dibayar');
$table->enum('metode_pembayaran', [
    'bank_transfer',
    'qris',
    'gopay',
    'shopeepay',
    'ovo',
    'credit_card',
    'cstore'
])->nullable();


    // $table->string('metode_pembayaran')->nullable(); // cash, transfer, midtrans
    $table->string('payment_reference')->nullable(); // id dari payment gateway
    $table->string('snap_token')->nullable(); // url untuk membayar (midtrans)
    // Waktu
    $table->timestamp('waktu_bayar')->nullable();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
