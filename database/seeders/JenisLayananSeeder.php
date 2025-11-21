<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisLayanan;

class JenisLayananSeeder extends Seeder
{
    public function run(): void
    {
        // Contoh: layanan milik mitra dengan ID 1
        JenisLayanan::create([
            'mitra_id' => 1,
            'nama_layanan' => 'Cuci Kering',
            'deskripsi' => 'Pakaian dicuci dan dikeringkan tanpa setrika',
            'satuan' => 'kg',
            'harga' => 7000,
        ]);

        JenisLayanan::create([
            'mitra_id' => 1,
            'nama_layanan' => 'Cuci Setrika',
            'deskripsi' => 'Pakaian dicuci, dikeringkan, dan disetrika rapi',
            'satuan' => 'kg',
            'harga' => 12000,
        ]);

        JenisLayanan::create([
            'mitra_id' => 1,
            'nama_layanan' => 'Setrika Saja',
            'deskripsi' => 'Hanya layanan setrika.',
            'satuan' => 'kg',
            'harga' => 8000,
        ]);
    }
}
