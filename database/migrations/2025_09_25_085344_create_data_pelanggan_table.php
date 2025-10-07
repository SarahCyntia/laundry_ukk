<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_pelanggan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');                // Nama pelanggan
            $table->enum('jenis_kelamin', ['L', 'P']); // L = Laki-laki, P = Perempuan
            $table->string('telepon')->unique();   // Nomor telepon
            $table->text('alamat');                // Alamat lengkap
            $table->timestamps();                  // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_pelanggan');
    }
};
