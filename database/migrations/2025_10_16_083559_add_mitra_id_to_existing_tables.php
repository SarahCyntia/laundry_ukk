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
    Schema::table('data_pelanggan', function (Blueprint $table) {
        $table->foreignId('mitra_id')->constrained('mitra')->onDelete('cascade');
    });

    Schema::table('pegawai_laundry', function (Blueprint $table) {
        $table->foreignId('mitra_id')->constrained('mitra')->onDelete('cascade');
    });

    // Schema::table('jenis_layanan', function (Blueprint $table) {
    //     $table->foreignId('mitra_id')->constrained('mitra')->onDelete('cascade');
        
    // });
    // Schema::table('transaksi', function (Blueprint $table) {
    //     $table->foreignId('mitra_id')->constrained('mitra')->onDelete('cascade');
    // });
    Schema::table('layanan_tambahan_transaksi', function (Blueprint $table) {
        $table->foreignId('mitra_id')->constrained('mitra')->onDelete('cascade');
    });
    Schema::table('users', function (Blueprint $table) {
        // $table->foreignId('mitra_id')->constrained(table: 'mitra')->onDelete('cascade');
        $table->unsignedBigInteger('mitra_id')->nullable(); // ✅ tambahkan nullable()

    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('existing_tables', function (Blueprint $table) {
            //
        });
    }
};
