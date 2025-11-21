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
        Schema::create('transaksilama', function (Blueprint $table) {
            $table->id();

            $table->string('kode_transaksi')->unique();

            $table->foreignId('pelanggan_id')->constrained('users');
            $table->foreignId('mitra_id')->constrained('mitra');
            $table->foreignId('kurir_pickup_id')->nullable()->constrained('kurir');
            $table->foreignId('kurir_antar_id')->nullable()->constrained('kurir');
            $table->foreignId('layanan_id')->constrained('jenis_layanan');

            $table->text('alamat_pickup');
            $table->text('alamat_dropoff');

            $table->decimal('berat', 8, 2)->nullable();

            $table->decimal('harga', 12, 2);
            $table->decimal('biaya_pickup', 12, 2)->default(0);
            $table->decimal('biaya_antar', 12, 2)->default(0);
            $table->decimal('total', 12, 2);

            $table->text('catatan')->nullable();

            $table->enum('metode_pembayaran', ['cash','transfer','ewallet'])->nullable();
            $table->string('bukti_pembayaran')->nullable();

            $table->enum('status', [
                'pending',
                'menunggu_kurir',
                'pickup',
                'dicuci',
                'selesai_cuci',
                'antar',
                'selesai',
                'dibatalkan'
            ])->default('pending');

            $table->datetime('tanggal_order');
            $table->datetime('tanggal_pickup')->nullable();
            $table->datetime('tanggal_selesai')->nullable();
            $table->datetime('tanggal_antar')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksilama');
    }
};
