<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisLayanan;
use App\Models\Mitra;


class JenisLayananSeeder extends Seeder
{
    public function run(): void
    {
        JenisLayanan::insert([
            [
                'mitra_id' => 1,
                'nama_layanan' => 'Cuci Kering',
                'deskripsi' => 'Pakaian dicuci dan dikeringkan tanpa setrika',
                'satuan' => 'kg',
                'harga' => 7000,
            ],
            [
                'mitra_id' => 1,
                'nama_layanan' => 'Cuci Setrika',
                'deskripsi' => 'Dicuci lalu disetrika rapi',
                'satuan' => 'kg',
                'harga' => 12000,
            ],
            [
                'mitra_id' => 1,
                'nama_layanan' => 'Setrika Saja',
                'deskripsi' => 'Hanya layanan setrika',
                'satuan' => 'kg',
                'harga' => 8000,
            ],
        ]);
    }
}
// class JenisLayananSeeder extends Seeder
// {
//     public function run(): void
//     {
//         $mitra = Mitra::first(); // ambil mitra pertama yang dibuat oleh UserSeeder

//         if (!$mitra) {
//             return;
//         }

//         JenisLayanan::create([
//             'mitra_id' => $mitra->id,
//             'nama_layanan' => 'Cuci Kering',
//             'deskripsi' => 'Pakaian dicuci dan dikeringkan tanpa setrika',
//             'satuan' => 'kg',
//             'harga' => 7000,
//         ]);

//         JenisLayanan::create([
//             'mitra_id' => $mitra->id,
//             'nama_layanan' => 'Cuci Setrika',
//             'deskripsi' => 'Pakaian dicuci, dikeringkan, dan disetrika rapi',
//             'satuan' => 'kg',
//             'harga' => 12000,
//         ]);

//         JenisLayanan::create([
//             'mitra_id' => $mitra->id,
//             'nama_layanan' => 'Setrika Saja',
//             'deskripsi' => 'Hanya layanan setrika.',
//             'satuan' => 'kg',
//             'harga' => 8000,
//         ]);
//     }
// }
//     public function run(): void
//     {
//         // Contoh: layanan milik mitra dengan ID 1
//         JenisLayanan::create([
//             'mitra_id' => 1,
//             'nama_layanan' => 'Cuci Kering',
//             'deskripsi' => 'Pakaian dicuci dan dikeringkan tanpa setrika',
//             'satuan' => 'kg',
//             'harga' => 7000,
//         ]);

//         JenisLayanan::create([
//             'mitra_id' => 1,
//             'nama_layanan' => 'Cuci Setrika',
//             'deskripsi' => 'Pakaian dicuci, dikeringkan, dan disetrika rapi',
//             'satuan' => 'kg',
//             'harga' => 12000,
//         ]);

//         JenisLayanan::create([
//             'mitra_id' => 1,
//             'nama_layanan' => 'Setrika Saja',
//             'deskripsi' => 'Hanya layanan setrika.',
//             'satuan' => 'kg',
//             'harga' => 8000,
//         ]);
//     }
// }
