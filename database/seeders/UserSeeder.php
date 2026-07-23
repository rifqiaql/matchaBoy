<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'mrs. admin (Owner)',
            'username' => 'admin', // TAMBAHKAN BARIS INI
            'email' => 'admin@matchaboy.com',
            'password' => Hash::make('#Matchaboy2026'), // Ingat ganti pakai password lu
            'role' => 'admin',
        ]);
    }
}
