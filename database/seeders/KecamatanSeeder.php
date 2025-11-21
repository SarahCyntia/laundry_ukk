<?php 

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;

class KecamatanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama' => 'Benowo'],
            ['nama' => 'Pakal'],
            ['nama' => 'Asemrowo'],
            ['nama' => 'Sukomanunggal'],
            ['nama' => 'Tandes'],
            ['nama' => 'Sambikerep'],
            ['nama' => 'Lakarsantri'],

            ['nama' => 'Rungkut'],
            ['nama' => 'Gunung Anyar'],
            ['nama' => 'Sukolilo'],
            ['nama' => 'Mulyorejo'],
            ['nama' => 'Tambaksari'],
            ['nama' => 'Gubeng'],
            ['nama' => 'Wonokromo'],

            ['nama' => 'Gayungan'],
            ['nama' => 'Jambangan'],
            ['nama' => 'Wonocolo'],
            ['nama' => 'Karang Pilang'],
            ['nama' => 'Wiyung'],

            ['nama' => 'Kenjeran'],
            ['nama' => 'Bulak'],
            ['nama' => 'Simokerto'],
            ['nama' => 'Pabean Cantikan'],
            ['nama' => 'Krembangan'],
            ['nama' => 'Semampir'],

            ['nama' => 'Genteng'],
            ['nama' => 'Tegalsari'],
            ['nama' => 'Bubutan'],
            ['nama' => 'Sawahan'],
        ];

        Kecamatan::insert($data);
    }
}
