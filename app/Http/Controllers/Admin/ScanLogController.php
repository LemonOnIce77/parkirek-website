<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParkingRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScanLogController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today();

        $status = strtoupper($request->string('status')->toString()); // ACTIVE / CLOSED
        $platNo = $request->string('plat_no')->toString();

        // ===== Cards =====
        $todayEntry = ParkingRecord::query()
            ->whereDate('jam_masuk', $today)
            ->count();

        $todayExit = ParkingRecord::query()
            ->whereNotNull('jam_keluar')
            ->whereDate('jam_keluar', $today)
            ->count();

        $currentlyParked = ParkingRecord::query()
            ->where('status', 'ACTIVE')
            ->whereNull('jam_keluar')
            ->count();

        $avgMinutes = (int) ParkingRecord::query()
            ->whereNotNull('jam_keluar')
            ->whereNotNull('jam_masuk')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, jam_masuk, jam_keluar)) as avg_min')
            ->value('avg_min');

        // ===== Table =====
        $query = ParkingRecord::query()
            ->with(['kendaraan']) // ✅ cukup ini (kendaraan punya jenis_kendaraan column)
            ->orderByRaw(
                "GREATEST(
                    COALESCE(jam_keluar,'1970-01-01'),
                    COALESCE(jam_masuk,'1970-01-01')
                ) DESC"
            );

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($platNo !== '') {
            $query->where(function ($q) use ($platNo) {
                $q->where('plat_snapshot', 'like', "%{$platNo}%")
                  ->orWhereHas('kendaraan', function ($qq) use ($platNo) {
                      $qq->where('plat_no', 'like', "%{$platNo}%");
                  });
            });
        }

        $records = $query->paginate(20)->withQueryString();

        return view('admin.vehicle_logs', [
            'records' => $records,
            'status'  => $status,
            'platNo'  => $platNo,
            'cards'   => [
                'today_entry'      => $todayEntry,
                'today_exit'       => $todayExit,
                'currently_parked' => $currentlyParked,
                'avg_minutes'      => $avgMinutes,
            ],
        ]);
    }
}
