<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('transaksi', function (Blueprint $table) {
        $table->id();

        // pelanggan
        $table->unsignedBigInteger('user_id');

        // laundry / mitra
        $table->unsignedBigInteger('mitra_id');

        // detail pesanan
        $table->integer('berat')->nullable(); // kg
        $table->integer('total_harga')->nullable();

        $table->enum('status', [
            'menunggu_konfirmasi',
            'diproses',
            'dijemput',
            'dicuci',
            'selesai',
            'dibatalkan'
        ])->default('menunggu_konfirmasi');

        $table->timestamps();

        // Relasi
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('mitra_id')->references('id')->on('mitra')->onDelete('cascade');
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
