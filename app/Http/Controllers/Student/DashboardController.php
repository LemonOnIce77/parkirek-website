<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\ParkingRecord;
use App\Models\Pelanggaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $userId = $user->id;

        $kendaraans = Kendaraan::query()
            ->where('user_id', $userId)
            ->orderBy('plat_no')
            ->get(['id', 'plat_no', 'stnk_number', 'jenis_kendaraan']);

        $kendaraanIds = $kendaraans->pluck('id');
        $platNos = $kendaraans->pluck('plat_no');

        $totalVehicles = $kendaraans->count();

        $totalViolations = Pelanggaran::query()
            ->where(function ($q) use ($kendaraanIds, $platNos) {
                $q->whereIn('kendaraan_id', $kendaraanIds)
                  ->orWhereIn('plat_no', $platNos);
            })
            ->count();

        // Ambil activity untuk logbook per kendaraan (dibatasi biar ringan)
        $parking = ParkingRecord::query()
            ->whereIn('kendaraan_id', $kendaraanIds)
            ->latest('jam_masuk')
            ->take(60)
            ->get(['kendaraan_id', 'jam_masuk', 'jam_keluar', 'status']);

        $violations = Pelanggaran::query()
            ->where(function ($q) use ($kendaraanIds, $platNos) {
                $q->whereIn('kendaraan_id', $kendaraanIds)
                  ->orWhereIn('plat_no', $platNos);
            })
            ->latest('created_at')
            ->take(30)
            ->get(['kendaraan_id', 'plat_no', 'jenis_pelanggaran', 'status', 'created_at']);

        // Buat events per kendaraan_id
        $eventsByVehicle = [];

        foreach ($kendaraans as $k) {
            $eventsByVehicle[$k->id] = collect();
        }

        foreach ($parking as $pr) {
            if (!isset($eventsByVehicle[$pr->kendaraan_id])) continue;

            $eventsByVehicle[$pr->kendaraan_id]->push([
                'time' => $pr->jam_masuk,
                'text' => 'Parking In',
                'dot'  => 'dot-blue',
            ]);

            if ($pr->jam_keluar) {
                $eventsByVehicle[$pr->kendaraan_id]->push([
                    'time' => $pr->jam_keluar,
                    'text' => 'Parking Out',
                    'dot'  => 'dot-green',
                ]);
            }
        }

        foreach ($violations as $v) {
            // Map pelanggaran ke kendaraan:
            // - kalau kendaraan_id ada -> pakai itu
            // - kalau null -> coba cocokkan via plat_no milik user
            $targetKendaraanId = $v->kendaraan_id;

            if (!$targetKendaraanId) {
                $match = $kendaraans->firstWhere('plat_no', $v->plat_no);
                $targetKendaraanId = $match?->id;
            }

            if ($targetKendaraanId && isset($eventsByVehicle[$targetKendaraanId])) {
                $eventsByVehicle[$targetKendaraanId]->push([
                    'time' => $v->created_at,
                    'text' => 'Violation: ' . $v->jenis_pelanggaran,
                    'dot'  => 'dot-red',
                ]);
            }
        }

        // sort & group per kendaraan (Today/Yesterday/DayName)
        $logbookTabs = [];

        foreach ($kendaraans as $k) {
            $events = ($eventsByVehicle[$k->id] ?? collect())
                ->filter(fn ($e) => !empty($e['time']))
                ->sortByDesc('time')
                ->take(20)
                ->values();

            $grouped = $events->groupBy(function ($e) {
                $d = \Carbon\Carbon::parse($e['time']);
                if ($d->isToday()) return 'Today';
                if ($d->isYesterday()) return 'Yesterday';
                return $d->translatedFormat('l');
            });

            $logbookTabs[] = [
                'kendaraan' => $k,
                'grouped'   => $grouped,
            ];
        }

        return view('student.dashboard.user', [
            'studentName'      => $user->nama ?? 'Student',
            'totalVehicles'    => $totalVehicles,
            'totalViolations'  => $totalViolations,
            'logbookTabs'      => $logbookTabs,
        ]);
    }
}
