<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kendaraan;
use App\Models\ParkingRecord;
use App\Models\User;
use Carbon\Carbon;

class ParkingRecordSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $kendaraan = Kendaraan::first();

        if (!$admin || !$kendaraan) {
            $this->command->warn('Admin atau Kendaraan tidak ditemukan. Seeder ParkingRecord dilewati.');
            return;
        }

        // 1) Record DONE (kemarin)
        $jamMasuk1 = Carbon::now()->subDay()->setTime(8, 15, 0);
        $jamKeluar1 = (clone $jamMasuk1)->addHours(3);

        ParkingRecord::create([
            'kendaraan_id'  => $kendaraan->id,
            'scanned_by'    => $admin->id,
            'jam_masuk'     => $jamMasuk1,
            'jam_keluar'    => $jamKeluar1,
            'plat_snapshot' => $kendaraan->plat_no,
            'stnk_snapshot' => $kendaraan->stnk_number,
            'status'        => 'DONE',
        ]);

        // 2) Record ACTIVE (hari ini)
        $jamMasuk2 = Carbon::now()->setTime(9, 30, 0);

        ParkingRecord::create([
            'kendaraan_id'  => $kendaraan->id,
            'scanned_by'    => $admin->id,
            'jam_masuk'     => $jamMasuk2,
            'jam_keluar'    => null,
            'plat_snapshot' => $kendaraan->plat_no,
            'stnk_snapshot' => $kendaraan->stnk_number,
            'status'        => 'ACTIVE',
        ]);
    }
}
