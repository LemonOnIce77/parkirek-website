<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // =====================
        // ADMIN
        // =====================
        User::create([
            'nama' => 'Admin Parkir',
            'email' => 'admin@parkir.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'area' => 'Gerbang Utama',
        ]);

        // =====================
        // MAHASISWA / STUDENT
        // =====================
        User::create([
            'nama' => 'Mahasiswa Demo',
            'email' => 'mahasiswa@parkir.test',
            'password' => Hash::make('password'),
            'role' => 'student',
            'area' => null,
        ]);
    }
}
