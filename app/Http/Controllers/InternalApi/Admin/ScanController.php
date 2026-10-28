<?php

namespace App\Http\Controllers\InternalApi\Admin;

use App\Http\Controllers\InternalApi\InternalApiController;
use App\Http\Requests\InternalApi\Admin\ScanRequest;
use App\Models\Kendaraan;
use App\Models\ParkingRecord;
use Illuminate\Support\Facades\DB;

class ScanController extends InternalApiController
{
    public function masuk(ScanRequest $request)
    {
        $qr = $request->validated()['qr_token'];

        $kendaraan = Kendaraan::query()->where('qr_token', $qr)->first();
        if (!$kendaraan) {
            return $this->fail('Kendaraan tidak ditemukan untuk QR token ini.', ['qr_token' => ['Not found']], 404);
        }

        $adminId = $request->user()->id;

        $record = DB::transaction(function () use ($kendaraan, $adminId) {
            return ParkingRecord::create([
                'kendaraan_id'  => $kendaraan->id,
                'scanned_by'    => $adminId,
                'jam_masuk'     => now(),
                'jam_keluar'    => null,
                'plat_snapshot' => $kendaraan->plat_no,
                'stnk_snapshot' => $kendaraan->stnk_number,
                'status'        => 'ACTIVE',
            ]);
        });

        return $this->ok('Scan MASUK berhasil.', [
            'parking_record_id' => $record->id,
            'plat_no' => $record->plat_snapshot,
            'jam_masuk' => $record->jam_masuk,
            'status' => $record->status,
        ]);
    }

    public function keluar(ScanRequest $request)
    {
        $qr = $request->validated()['qr_token'];

        $kendaraan = Kendaraan::query()->where('qr_token', $qr)->first();
        if (!$kendaraan) {
            return $this->fail('Kendaraan tidak ditemukan untuk QR token ini.', ['qr_token' => ['Not found']], 404);
        }

        $adminId = $request->user()->id;

        $updated = DB::transaction(function () use ($kendaraan, $adminId) {
            $record = ParkingRecord::query()
                ->where('kendaraan_id', $kendaraan->id)
                ->where('status', 'ACTIVE')
                ->whereNull('jam_keluar')
                ->orderByDesc('jam_masuk')
                ->lockForUpdate()
                ->first();

            if (!$record) {
                return null;
            }

            $record->update([
                'jam_keluar' => now(),
                'status'     => 'DONE',
                'scanned_by' => $adminId, // admin yang scan keluar
            ]);

            return $record;
        });

        if (!$updated) {
            return $this->fail(
                'Tidak ada record ACTIVE untuk kendaraan ini (mungkin belum masuk atau sudah keluar).',
                ['qr_token' => ['No ACTIVE record']],
                409
            );
        }

        return $this->ok('Scan KELUAR berhasil.', [
            'parking_record_id' => $updated->id,
            'plat_no' => $updated->plat_snapshot,
            'jam_masuk' => $updated->jam_masuk,
            'jam_keluar' => $updated->jam_keluar,
            'status' => $updated->status,
        ]);
    }
}

