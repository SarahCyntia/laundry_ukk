<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Mitra;
use App\Models\Pelanggan;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // =======================
        // ADMIN
        // =======================
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'phone' => '08123456789',
        ]);
        $admin->assignRole('admin');

        // =======================
        // PEGAWAI
        // =======================
        $pegawai = User::create([
            'name' => 'Pegawai',
            'email' => 'pegawai@gmail.com',
            'password' => bcrypt('12345678'),
            'phone' => '08123456788',
        ]);
        $pegawai->assignRole('pegawai');

        // =======================
        // MITRA (Laundry)
        // =======================
        $mitraUser = User::create([
            'name' => 'Mitra Laundry',
            'email' => 'mitra@gmail.com',
            'password' => bcrypt('12345678'),
            'phone' => '08123459998',
        ]);

        $mitraUser->assignRole('mitra');

        $mitra = Mitra::create([
            'user_id' => $mitraUser->id,
            'nama_laundry' => 'Laundry Mitra',
            'alamat_laundry' => 'Jl. Contoh No.10',
            'kecamatan_id' => 1,
            'foto_ktp' => 'default.png',
            'foto_toko' => null,
            'status_toko' => 'buka',
            'status_validasi' => 'diterima',
            'jam_buka' => '08:00',
            'jam_tutup' => '20:00',
        ]);

        // =======================
        // PELANGGAN
        // =======================
        $pelangganUser = User::create([
            'name' => 'Pelanggan 1',
            'email' => 'pelanggan@gmail.com',
            'password' => bcrypt('12345678'),
            'phone' => '08123859998',
        ]);

        $pelangganUser->assignRole('pelanggan');

        Pelanggan::create([
            'user_id' => $pelangganUser->id,
            'alamat' => 'Jl. Pelanggan No.5',
            'kode_pos' => '12345',
            'kecamatan_id' => 1,
        ]);
    }
}
// class UserSeeder extends Seeder
// {
//     public function run(): void
//     {
//         /*
//         |--------------------------------------------------------------------------
//         | ADMIN
//         |--------------------------------------------------------------------------
//         */
//         $admin = User::create([
//             'name' => 'Admin',
//             'email' => 'admin@gmail.com',
//             'password' => bcrypt('12345678'),
//             'phone' => '08123456789',
//         ]);
//         $admin->assignRole('admin');


//         /*
//         |--------------------------------------------------------------------------
//         | PEGAWAI
//         |--------------------------------------------------------------------------
//         */
//         $pegawai = User::create([
//             'name' => 'Pegawai',
//             'email' => 'pegawai@gmail.com',
//             'password' => bcrypt('12345678'),
//             'phone' => '08123456788',
//         ]);
//         $pegawai->assignRole('pegawai');


//         /*
//         |--------------------------------------------------------------------------
//         | MITRA
//         |--------------------------------------------------------------------------
//         */
//         $mitraUser = User::create([
//             'name' => 'mitra',
//             'email' => 'mitra@gmail.com',
//             'password' => bcrypt('12345678'),
//             'phone' => '08123459998',
//         ]);

//         // Buat record mitra
//         $mitra = Mitra::create([
//             'user_id' => $mitraUser->id,
//             'nama_laundry' => 'Laundry Mitra',
//             'alamat_laundry' => 'Jl. Contoh No. 10',
//             'kecamatan_id' => '1',
//             'foto_ktp' => 'default-ktp.png',
//             'foto_toko' => null,
//             'status_toko' => 'buka',
//             'status_validasi' => 'diterima',
//             'jam_buka' => '08:00',
//             'jam_tutup' => '20:00',
//             'kecamatan_id' => '1',
//         ]);

//         $mitraUser->assignRole('mitra');


//         /*
//         |--------------------------------------------------------------------------
//         | PELANGGAN
//         |--------------------------------------------------------------------------
//         */
//         $pelangganUser = User::create([
//             'name' => 'pelanggan',
//             'email' => 'pelanggan@gmail.com',
//             'password' => bcrypt('12345678'),
//             'phone' => '08123859998',
//         ]);

//         Pelanggan::create([
//             'user_id' => $pelangganUser->id,
//             'alamat' => 'Jl. Pelanggan No. 5',
//         ]);

//         $pelangganUser->assignRole('pelanggan');
//     }
// }




