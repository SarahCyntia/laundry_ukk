
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rating_transaksi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaksi_id')->constrained('transaksilama')->onDelete('cascade');
            $table->foreignId('pelanggan_id')->constrained('users');

            $table->tinyInteger('rating');
            $table->text('feedback')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rating_transaksi');
    }
};
