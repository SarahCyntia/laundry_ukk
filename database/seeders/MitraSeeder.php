<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Mitra;

class MitraSeeder extends Seeder
{
    public function run()
    {
        $users = User::where('role', 'mitra')->get();

        foreach ($users as $user) {
            Mitra::create([
                'user_id' => $user->id,
                'nama_toko' => fake()->company(),
                'alamat' => fake()->address(),
                'telepon' => fake()->phoneNumber(),
                'latitude' => fake()->latitude(-6.2, -6.1),
                'longitude' => fake()->longitude(106.7, 106.9),
            ]);
        }
    }
}