<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->string('snap_token')->nullable()->after('harga_final');
            $table->string('status_pembayaran')->default('pending')->after('snap_token');
        });
    }

    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'status_pembayaran']);
        });
    }
};
