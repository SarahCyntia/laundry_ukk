
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tracking_transaksi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaksi_id')->constrained('transaksilama')->onDelete('cascade');
            $table->foreignId('kurir_id')->nullable()->constrained('kurir');

            $table->enum('status', [
                'pickup',
                'menuju_mitra',
                'dicuci',
                'selesai_cuci',
                'menuju_antar',
                'selesai',
            ]);

            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_transaksi');
    }
};
