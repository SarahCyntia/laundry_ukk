<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_layanan', function (Blueprint $table) {
            $table->id();

            // Relasi ke mitra
            $table->unsignedBigInteger('mitra_id');
            $table->string('nama_layanan');
            $table->text('deskripsi')->nullable();

            // Satuan lebih fleksibel
            $table->string('satuan')->default('kg');

            // Harga
            $table->decimal('harga', 12, 2)->unsigned();

            $table->timestamps();

            // Foreign key
            $table->foreign('mitra_id')
                ->references('id')
                ->on('mitra') // nama tabel benar
                ->onDelete('cascade');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_layanan');
    }
};
