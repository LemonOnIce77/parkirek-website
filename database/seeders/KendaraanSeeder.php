<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Kendaraan;
use App\Models\User;

class KendaraanSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user mahasiswa
        $mahasiswa = User::where('role', 'student')->first();

        if (!$mahasiswa) {
            $this->command->warn('Mahasiswa tidak ditemukan. Seeder Kendaraan dilewati.');
            return;
        }

        Kendaraan::create([
            'user_id' => $mahasiswa->id,
            'jenis_kendaraan' => 'motor',
            'plat_no' => 'B 1234 ABC',
            'stnk_number' => 'STNK-001',
            'stnk_photo_path' => null,
            'qr_token' => (string) Str::uuid(),
        ]);

        Kendaraan::create([
            'user_id' => $mahasiswa->id,
            'jenis_kendaraan' => 'mobil',
            'plat_no' => 'B 5678 XYZ',
            'stnk_number' => 'STNK-002',
            'stnk_photo_path' => null,
            'qr_token' => (string) Str::uuid(),
        ]);
    }
}
