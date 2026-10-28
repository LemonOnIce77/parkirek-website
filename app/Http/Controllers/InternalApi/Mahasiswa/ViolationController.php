<?php

namespace App\Http\Controllers\InternalApi\Mahasiswa;

use App\Http\Controllers\InternalApi\InternalApiController;
use App\Http\Requests\InternalApi\Mahasiswa\FilterViolationRequest;
use App\Models\Kendaraan;
use App\Models\Pelanggaran;

class ViolationController extends InternalApiController
{
    public function index(FilterViolationRequest $request)
    {
        $userId = $request->user()->id;
        $f = $request->validated();
        $perPage = $f['per_page'] ?? 15;

        $kendaraanIds = Kendaraan::query()
            ->where('user_id', $userId)
            ->pluck('id');

        $platList = Kendaraan::query()
            ->where('user_id', $userId)
            ->pluck('plat_no');

        $query = Pelanggaran::query()
            ->with(['kendaraan', 'creator:id,nama'])
            ->where(function ($q) use ($kendaraanIds, $platList) {
                if ($kendaraanIds->isNotEmpty()) {
                    $q->whereIn('kendaraan_id', $kendaraanIds);
                }
                if ($platList->isNotEmpty()) {
                    $q->orWhereIn('plat_no', $platList);
                }
            })
            ->orderByDesc('created_at');

        if (!empty($f['status'])) {
            $query->where('status', $f['status']);
        }

        $paginator = $query->paginate($perPage)->withQueryString();

        return $this->paginated($paginator, 'Violations fetched.');
    }
}
