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
    Schema::create('pelanggan', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

        $table->unsignedBigInteger('kecamatan_id')->nullable();
$table->foreign('kecamatan_id')->references('id')->on('kecamatan')->onDelete('cascade');

        $table->text('alamat')->nullable();
        $table->string('kode_pos')->nullable();
        $table->timestamps();
    });

}
    // public function up(): void
    // {
    //     Schema::create('pelanggan', function (Blueprint $table) {
    //         $table->id();
    //         $table->timestamps();
    //     });
    // }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};
