<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Pelanggan;
use App\Models\Mitra;
use App\Models\JenisLayanan;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $pelanggan = Pelanggan::pluck('id')->toArray();
        $mitra = Mitra::pluck('id')->toArray();
        $layanan = JenisLayanan::pluck('id')->toArray();

        if (count($pelanggan) == 0 || count($mitra) == 0 || count($layanan) == 0) {
            dd("Seeder GAGAL karena pelanggan/mitra/jenis_layanan masih kosong!");
        }

        $data = [
            [
                'pelanggan_id'      => $pelanggan[0],
                'mitra_id'          => $mitra[0],
                'jenis_layanan_id'  => $layanan[0],
                'kode_order'        => 'ORD-1001',
                'harga_final'        => '15000',
                'berat_estimasi'    => 3,
                'catatan'           => 'Tolong cepat ya',
                'status'            => 'menunggu_konfirmasi_mitra',
            ],
            [
                'pelanggan_id'      => $pelanggan[1] ?? $pelanggan[0],
                'mitra_id'          => $mitra[0],
                'jenis_layanan_id'  => $layanan[1] ?? $layanan[0],
                'kode_order'        => 'ORD-1002',
                'harga_final'        => '15000',
                'berat_estimasi'    => 5,
                'catatan'           => 'Pakaian kantor',
                'status'            => 'diterima',
            ],
            [
                'pelanggan_id'      => $pelanggan[2] ?? $pelanggan[0],
                'mitra_id'          => $mitra[1] ?? $mitra[0],
                'jenis_layanan_id'  => $layanan[2] ?? $layanan[0],
                'kode_order'        => 'ORD-1003',
                'harga_final'        => '15000',
                'berat_estimasi'    => 2,
                'catatan'           => null,
                'status'            => 'diproses',
            ],
        ];

        foreach ($data as $row) {
            Order::create($row);
        }
    }
}
// {
//     public function run()
//     {
//         $pelanggan = Pelanggan::first();
//         $mitra = Mitra::first();
//         $jenis_layanan = JenisLayanan::first();

//         Order::create([
//             'pelanggan_id' => $pelanggan->id,
//             'mitra_id' => $mitra->id,
//             'jenis_layanan_id' => $jenis_layanan->id,
//             'kode_order' => 'ORD-'.time(),
//             'berat_estimasi' => 3.5,
//             'catatan' => 'Tolong cepat ya',
//             'status' => 'menunggu_konfirmasi_mitra'
//         ]);
//     }
// }