<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisItem;

class JenisItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisItems = [
            [
                'nama' => 'Pakaian Sehari-hari',
                'deskripsi' => 'Kaos, celana, kemeja casual',
                'cabang_id' => 1,
            ],
            [
                'nama' => 'Pakaian Formal',
                'deskripsi' => 'Jas, gaun, kemeja formal',
                'cabang_id' => 1,
            ],
            [
                'nama' => 'Pakaian Khusus',
                'deskripsi' => 'Kebaya, gaun pesta, baju adat',
                'cabang_id' => 1,
            ],
            [
                'nama' => 'Sepatu',
                'deskripsi' => 'Sepatu kulit, sneakers, sandal',
                'cabang_id' => 1,
            ],
            [
                'nama' => 'Perlengkapan Tidur',
                'deskripsi' => 'Seprai, sarung bantal, bed cover',
                'cabang_id' => 1,
            ],
            [
                'nama' => 'Gorden',
                'deskripsi' => 'Gorden rumah, kantor',
                'cabang_id' => 1,
            ],
            [
                'nama' => 'Bantal & Guling',
                'deskripsi' => 'Bantal tidur, bantal sofa, guling',
                'cabang_id' => 1,
            ],
            [
                'nama' => 'Karpet',
                'deskripsi' => 'Karpet rumah, karpet masjid',
                'cabang_id' => 1,
            ],
        ];

        foreach ($jenisItems as $item) {
            JenisItem::create($item);
        }
    }
} 