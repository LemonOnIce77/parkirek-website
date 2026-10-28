<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mahasiswa\FilterScanLogRequest   ;
use App\Models\ParkingRecord;
use Illuminate\View\View;

class ScanLogController extends Controller
{
    public function index(FilterScanLogRequest $request): View
    {
        $userId = $request->user()->id;
        $filters = $request->validated();

        $query = ParkingRecord::query()
            // Pastikan hanya log kendaraan milik mahasiswa
            ->whereHas('kendaraan', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            // ambil relasi kendaraan + admin scanner (optional)
            ->with([
                'kendaraan',
                'scanner:id,nama',
            ])
            ->latest('jam_masuk');

        // Filter: status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter: from (jam_masuk >= from 00:00:00)
        if (!empty($filters['from'])) {
            $query->where('jam_masuk', '>=', $filters['from'] . ' 00:00:00');
        }

        // Filter: to (jam_masuk <= to 23:59:59)
        if (!empty($filters['to'])) {
            $query->where('jam_masuk', '<=', $filters['to'] . ' 23:59:59');
        }

        // Filter: plat_no like (plat_snapshot)
        if (!empty($filters['plat_no'])) {
            $query->where('plat_snapshot', 'like', '%' . $filters['plat_no'] . '%');
        }

        $records = $query->paginate(15)->withQueryString();

        return view('mahasiswa.scan_logs.index', compact('records', 'filters'));
    }
}
