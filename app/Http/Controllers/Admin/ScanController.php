<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreScanRequest;
use App\Models\Kendaraan;
use App\Models\ParkingRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScanController extends Controller
{
    public function index(): View
    {
        // pastikan view kamu benar: resources/views/admin/scan.blade.php
        return view('admin.scan');
    }

    public function scanMasuk(StoreScanRequest $request)
    {
        $data = $request->validated();

        // Guard action biar endpoint gak disalahgunakan
        abort_unless(($data['action'] ?? null) === 'masuk', 422, 'Action tidak sesuai endpoint.');

        $kendaraan = Kendaraan::query()
            ->where('qr_token', $data['qr_token'])
            ->first();

        if (!$kendaraan) {
            return $this->fail($request, 'Kendaraan tidak ditemukan untuk QR token ini.');
        }

        $adminId = $request->user()->id;

        // Cegah double masuk: kalau masih ACTIVE, jangan buat record baru
        $hasActive = ParkingRecord::query()
            ->where('kendaraan_id', $kendaraan->id)
            ->where('status', 'ACTIVE')
            ->whereNull('jam_keluar')
            ->exists();

        if ($hasActive) {
            return $this->fail($request, "Plat {$kendaraan->plat_no} masih berstatus ACTIVE (sudah masuk dan belum keluar).");
        }

        DB::transaction(function () use ($kendaraan, $adminId) {
            ParkingRecord::create([
                'kendaraan_id'   => $kendaraan->id,
                'scanned_by'     => $adminId,
                'jam_masuk'      => now(),
                'jam_keluar'     => null,
                'plat_snapshot'  => $kendaraan->plat_no,
                'stnk_snapshot'  => $kendaraan->stnk_number,
                'status'         => 'ACTIVE',
            ]);
        });

        return $this->success($request, $kendaraan, 'masuk', "Scan MASUK berhasil.");
    }

    public function scanKeluar(StoreScanRequest $request)
    {
        $data = $request->validated();

        abort_unless(($data['action'] ?? null) === 'keluar', 422, 'Action tidak sesuai endpoint.');

        $kendaraan = Kendaraan::query()
            ->where('qr_token', $data['qr_token'])
            ->first();

        if (!$kendaraan) {
            return $this->fail($request, 'Kendaraan tidak ditemukan untuk QR token ini.');
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

            if (!$record) return false;

            $record->update([
                'jam_keluar' => now(),
                'status'     => 'DONE',
                'scanned_by' => $adminId, // admin yang scan keluar
            ]);

            return true;
        });

        if (!$updated) {
            return $this->fail($request, "Tidak ada parking record ACTIVE untuk plat {$kendaraan->plat_no} (mungkin belum scan masuk / sudah keluar).");
        }

        return $this->success($request, $kendaraan, 'keluar', "Scan KELUAR berhasil.");
    }

    private function success(Request $request, Kendaraan $kendaraan, string $mode, string $message)
    {
        // AJAX JSON (buat blade scan yang pakai fetch)
        if ($request->expectsJson()) {
            return response()->json([
                'ok'        => true,
                'message'   => $message,
                'plate'     => $kendaraan->plat_no,
                'owner'     => optional($kendaraan->user)->nama ?? optional($kendaraan->user)->email ?? '-',
                'timestamp' => now()->format('H:i'),
                'mode'      => $mode,
            ]);
        }

        // fallback: redirect biasa
        return redirect()
            ->route('admin.scan.index')
            ->with('success', "{$message} Plat {$kendaraan->plat_no}.");
    }

    private function fail(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'ok'      => false,
                'message' => $message,
            ], 422);
        }

        return back()->withErrors(['qr_token' => $message])->withInput();
    }
}
