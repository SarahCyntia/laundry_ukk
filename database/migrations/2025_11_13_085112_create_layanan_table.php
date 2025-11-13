php artisan make:model Layanan -m
<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLayananTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('layanan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mitra_id');
            $table->string('nama');
            $table->unsignedDecimal('harga', 12, 2);
            $table->enum('satuan', ['kiloan', 'satuan'])->default('satuan');
            $table->timestamps();

            // foreign key ke tabel mitra (pastikan tabel mitra ada dan kolom id bertipe unsignedBigInteger)
            $table->foreign('mitra_id')->references('id')->on('mitra')->onDelete('cascade')->onUpdate('cascade');

            // index untuk pencarian cepat
            $table->index(['mitra_id', 'nama']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('layanan', function (Blueprint $table) {
            $table->dropForeign(['mitra_id']);
            $table->dropIndex(['mitra_id', 'nama']);
        });

        Schema::dropIfExists('layanan');
    }
}
