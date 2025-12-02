<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiTable extends Migration
{
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('order_id'); // relasi ke order
    $table->unsignedBigInteger('user_id')->nullable(); 
    $table->unsignedBigInteger('mitra_id')->nullable();

    $table->integer('berat')->nullable();
    $table->bigInteger('harga_final')->nullable();

    // MIDTRANS DATA
    $table->string('midtrans_order_id')->nullable(); 
    $table->string('snap_token')->nullable();
    $table->string('payment_type')->nullable();
    $table->string('payment_code')->nullable();
    $table->string('status_transaksi')->default('pending'); 
    $table->text('pdf_url')->nullable();

    $table->timestamps();

    $table->foreign('order_id')->references('id')->on('order')->onDelete('cascade');
    $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
    $table->foreign('mitra_id')->references('id')->on('mitra')->nullOnDelete();
});

    }

    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
}
