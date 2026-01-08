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
    Schema::create('order', function (Blueprint $table) {
        $table->id();

        // Relasi
        $table->unsignedBigInteger('pelanggan_id'); // customer yang pesan
        $table->unsignedBigInteger('mitra_id'); // laundry yang dipilih customer
        // $table->unsignedBigInteger('jenis_layanan_id');
$table->unsignedBigInteger('jenis_layanan_id')->nullable();


        // Detail order
        $table->string('kode_order')->unique();
        $table->decimal('berat_estimasi', 5, 2)->nullable();
        $table->decimal('berat_aktual', 5, 2)->nullable();
        $table->integer('harga_final')->nullable();
        $table->string('foto_struk')->nullable();
        $table->text('catatan')->nullable();

        // Status utama
        $table->enum('status', [
            'menunggu_konfirmasi_mitra', // setelah customer pesan
            'ditunggu_mitra',
            'diterima',                  // mitra menerima
            'ditolak',                   // mitra menolak
            'diproses',                  // setelah customer antar & berat aktual dicatat
            'dicuci',
            'dikeringkan',
            'disetrika',
            'siap_diambil',
            'selesai'
        ])->default('menunggu_konfirmasi_mitra');

        // Alasan kalau ditolak
        $table->string('alasan_penolakan')->nullable();

        // Waktu-waktu penting
        // $table->timestamp('sudah_antar')->nullable();
        $table->timestamp('estimasi_selesai')->nullable();
        $table->timestamp('estimasi_jam')->nullable();
        $table->timestamp('waktu_pelanggan_antar')->nullable();
        $table->timestamp('waktu_diambil')->nullable();
         $table->integer('biaya')->nullable();
        $table->enum('status_pembayaran', ['belum dibayar', 'settlement', 'pending', 'expire', 'cancel', 'deny', 'failure', 'refund'])->default('belum dibayar');
        $table->timestamp('waktu')->useCurrent();


        $table->timestamps();

        // Foreign key
        $table->foreign('pelanggan_id')->references('id')->on('pelanggan')->onDelete('cascade');
        $table->foreign('mitra_id')->references('id')->on('mitra')->onDelete('cascade');
        $table->foreign('jenis_layanan_id')->references('id')->on('jenis_layanan')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};
