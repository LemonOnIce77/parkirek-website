<?php

namespace App\Http\Controllers\InternalApi\Admin;

use App\Http\Controllers\InternalApi\InternalApiController;
use App\Models\Kendaraan;
use Illuminate\Http\Request;

class KendaraanLookupController extends InternalApiController
{
    public function lookup(Request $request)
    {
        $qr = $request->query('qr_token');
        $plat = $request->query('plat_no');

        if (empty($qr) && empty($plat)) {
            return $this->fail('Isi salah satu: qr_token atau plat_no.', [
                'qr_token' => ['optional'],
                'plat_no'  => ['optional'],
            ], 422);
        }

        $query = Kendaraan::query()->with('user:id,nama');

        if (!empty($qr)) {
            $query->where('qr_token', $qr);
        } else {
            $query->where('plat_no', $plat);
        }

        $kendaraan = $query->first();

        if (!$kendaraan) {
            return $this->fail('Kendaraan tidak ditemukan.', null, 404);
        }

        return $this->ok('Kendaraan ditemukan.', [
            'id' => $kendaraan->id,
            'user' => $kendaraan->user ? [
                'id' => $kendaraan->user->id,
                'nama' => $kendaraan->user->nama,
            ] : null,
            'jenis_kendaraan' => $kendaraan->jenis_kendaraan ?? null,
            'plat_no' => $kendaraan->plat_no,
            'stnk_number' => $kendaraan->stnk_number,
            'qr_token' => $kendaraan->qr_token,
        ]);
    }
}
