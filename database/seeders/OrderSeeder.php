<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Pelanggan;
use App\Models\Mitra;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $pelanggan = Pelanggan::all();
        $mitra = Mitra::all();

        foreach (range(1, 5) as $i) {

            Order::create([
                'pelanggan_id' => $pelanggan->random()->id,
                'mitra_id' => $mitra->random()->id,
                'kode_order' => 'ORD-' . time() . $i,
                'berat_estimasi' => rand(1, 5),
                'catatan' => 'Pakaian kerja',
                'status' => 'menunggu_konfirmasi_mitra',
                'jenis_layanan_id' => '1',
            ]);
        }
    }
}
