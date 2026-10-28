<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kendaraan;
use App\Models\Pelanggaran;
use App\Models\User;
use Carbon\Carbon;

class PelanggaranSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $kendaraan = Kendaraan::first();

        if (!$admin) {
            $this->command->warn('Admin tidak ditemukan. Seeder Pelanggaran dilewati.');
            return;
        }

        // 1) Pelanggaran OPEN (kendaraan terdaftar)
        if ($kendaraan) {
            Pelanggaran::create([
                'kendaraan_id'      => $kendaraan->id,
                'created_by'        => $admin->id,
                'plat_no'           => $kendaraan->plat_no,
                'jenis_pelanggaran' => 'Parkir di area terlarang',
                'deskripsi'         => 'Ditemukan parkir melewati garis batas.',
                'denda'             => 50000,
                'photo_path'        => null, // seeder tidak upload file
                'status'            => 'OPEN',
                'created_at'        => Carbon::now()->subDays(2),
                'updated_at'        => Carbon::now()->subDays(2),
            ]);
        }

        // 2) Pelanggaran CLOSED (plat tidak terdaftar) - kendaraan_id null
        Pelanggaran::create([
            'kendaraan_id'      => null,
            'created_by'        => $admin->id,
            'plat_no'           => 'B 9999 TEST',
            'jenis_pelanggaran' => 'Tidak memakai stiker parkir',
            'deskripsi'         => null,
            'denda'             => 25000,
            'photo_path'        => null,
            'status'            => 'CLOSED',
            'created_at'        => Carbon::now()->subDay(),
            'updated_at'        => Carbon::now()->subDay(),
        ]);
    }
}
