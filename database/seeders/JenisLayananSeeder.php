<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisLayanan;

class JenisLayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisLayanan = [
            [
                // 'mitra_id' => 1,
                'nama_layanan' => 'Laundry Kiloan',
                'deskripsi' => 'Pakaian dicuci berdasarkan berat per kilogram, cocok untuk pakaian harian dalam jumlah banyak.',
            ],
            [
                // 'mitra_id' => 1,
                'nama_layanan' => 'Laundry Satuan',
                'deskripsi' => 'Pencucian per item, seperti jas, gaun, kebaya, atau selimut yang butuh perhatian khusus.',

            ],
            [
                // 'mitra_id' => 1,
                'nama_layanan' => 'Dry Cleaning',
                'deskripsi' => 'Pencucian tanpa air menggunakan cairan kimia khusus untuk bahan sensitif seperti sutra atau wol.',

            ],
            [
                // 'mitra_id' => 1,
                'nama_layanan' => 'Cuci Setrika (Wash & Iron)',
                'deskripsi' => 'Pakaian dicuci dan disetrika hingga rapi dan wangi, siap langsung dipakai.',
                
            ],
            [
                // 'mitra_id' => 1,
                'nama_layanan' => 'Cuci Lipat (Wash & Fold)',
                'deskripsi' => 'Pakaian hanya dicuci dan dilipat tanpa proses setrika, praktis untuk pakaian santai.',
                
            ],
            [
                // 'mitra_id' => 1,
                'nama_layanan' => 'Setrika Saja',
                'deskripsi' => 'Layanan hanya penyetrikaan untuk pakaian yang sudah dicuci sendiri di rumah.',
                
            ],
            [
                // 'mitra_id' => 1,
                'nama_layanan' => 'Laundry Express',
                'deskripsi' => 'Layanan cuci cepat dengan waktu pengerjaan 3–6 jam atau 1 hari, cocok untuk kebutuhan mendesak.',
                
            ],
            [
                // 'mitra_id' => 1,
                'nama_layanan' => 'Laundry Sepatu & Tas',
                'deskripsi' => 'Perawatan khusus sepatu dan tas dengan teknik aman sesuai material.',
                
            ],
            [
                // 'mitra_id' => 1,
                'nama_layanan' => 'Laundry Karpet & Gorden',
                'deskripsi' => 'Pencucian khusus untuk karpet, gorden, atau barang besar agar bersih dan wangi.',
                
            ],
            [
                // 'mitra_id' => 1,
                'nama_layanan' => 'Laundry Boneka',
                'deskripsi' => 'Membersihkan boneka agar tetap lembut, bersih, dan aman untuk anak-anak.',
                
            ],
            [
                // 'mitra_id' => 1,
                'nama_layanan' => 'Laundry Hotel & Restoran',
                'deskripsi' => 'Layanan skala besar untuk sprei, selimut, seragam, dan linen dengan standar kebersihan tinggi.',
                
            ],
        ];

        foreach ($jenisLayanan as $item) {
            JenisLayanan::create($item);
        }
    }
}
