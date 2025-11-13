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
    Schema::create('mitra', function (Blueprint $table) {
        $table->id();
        
        $table->string('nama_laundry');
        $table->string('user_id')->contrained('users')->onDelete('cascade');
        
        $table->enum('status_validasi', ['menunggu', 'diterima', 'ditolak'])->default('menunggu');
       $table->string('alamat_laundry');
        $table->string('foto_ktp');
        $table->enum('status_toko', ['buka', 'tutup'])->default('buka')->nullable();
         $table->softDeletes();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitra');
    }
};
