<?php

namespace App\Http\Controllers\InternalApi\Mahasiswa;

use App\Http\Controllers\InternalApi\InternalApiController;
use App\Http\Requests\InternalApi\Mahasiswa\FilterScanLogRequest;
use App\Models\ParkingRecord;

class ScanLogController extends InternalApiController
{
    public function index(FilterScanLogRequest $request)
    {
        $userId = $request->user()->id;
        $f = $request->validated();
        $perPage = $f['per_page'] ?? 15;

        $query = ParkingRecord::query()
            ->whereHas('kendaraan', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->with([
                'kendaraan',
                'scanner:id,nama',
            ])
            ->orderByDesc('jam_masuk');

        if (!empty($f['status'])) {
            $query->where('status', $f['status']);
        }

        if (!empty($f['from'])) {
            $query->where('jam_masuk', '>=', $f['from'] . ' 00:00:00');
        }

        if (!empty($f['to'])) {
            $query->where('jam_masuk', '<=', $f['to'] . ' 23:59:59');
        }

        if (!empty($f['plat_no'])) {
            $query->where('plat_snapshot', 'like', '%' . $f['plat_no'] . '%');
        }

        $paginator = $query->paginate($perPage)->withQueryString();

        return $this->paginated($paginator, 'Scan logs fetched.');
    }
}
