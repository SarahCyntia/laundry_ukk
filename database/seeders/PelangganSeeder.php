<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pelanggan;

class PelangganSeeder extends Seeder
{
    public function run()
    {
        $users = User::where('role', 'pelanggan')->get();

        foreach ($users as $user) {
            Pelanggan::create([
                'user_id' => $user->id,
                'alamat' => fake()->address(),
            ]);
        }
    }
}