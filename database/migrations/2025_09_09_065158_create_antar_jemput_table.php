<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('antar_jemput', function (Blueprint $table) {
            $table->id(); // Primary Key tabel ini

            // foreign key ke pesanan
            $table->unsignedBigInteger('id_pesanan')->nullable();
            $table->foreign('id_pesanan')
                ->references('id')->on('pesanan')
                ->onUpdate('set null')
                ->onDelete('set null');

            // foreign key ke pelanggan
            $table->unsignedBigInteger('id_pelanggan')->nullable();
            $table->foreign('id_pelanggan')
                ->references('id')->on('pelanggan')
                ->onUpdate('set null')
                ->onDelete('set null');

            $table->string('tracking_code', 20);
            $table->string('nama_pelanggan', 100);
            $table->string('no_hp', 15)->nullable();

            $table->enum('layanan', ['antar', 'jemput', 'antar-jemput'])->default('antar');
            $table->text('alamat_antar')->nullable();
            $table->text('alamat_jemput')->nullable();

            $table->enum('layanan_laundry', ['daily_kiloan', 'cuci_setrika', 'laundry_sepatu', 'laundry_tas', 'laundry_sarung_bantal', 'laundry_bed_cover', 'laundry_sprei', 'laundry_boneka'])->default('daily_kiloan');
            

            $table->enum('status', ['baru', 'proses', 'siap_ambil', 'antar', 'jemput', 'selesai', 'batal'])->default('baru');
            // $table->enum('status', ['menunggu', 'dalam_perjalanan', 'selesai'])->default('menunggu');
            $table->dateTime('selesai_at')->nullable();
            $table->dateTime('waktu_antar')->nullable();
            $table->dateTime('waktu_jemput')->nullable();

            $table->decimal('harga', 10, 2)->default(5000);

            $table->enum('status_pembayaran', ['belum dibayar', 'sudah dibayar'])->default('belum dibayar');

            $table->softDeletes();
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('antar_jemput');
    }
};
