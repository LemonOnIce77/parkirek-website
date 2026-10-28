<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\Pelanggaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ViolationController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        $kendaraans = Kendaraan::query()
            ->where('user_id', $userId)
            ->select('id', 'plat_no')
            ->get();

        $kendaraanIds = $kendaraans->pluck('id')->filter()->values();
        $platList     = $kendaraans->pluck('plat_no')->filter()->values();

        $violations = Pelanggaran::query()
            ->with([
                'kendaraan:id,plat_no,jenis_kendaraan',
                'creator' // ✅ jangan pilih kolom 'name'
            ])
            ->when($kendaraanIds->isNotEmpty() || $platList->isNotEmpty(), function ($q) use ($kendaraanIds, $platList) {
                $q->where(function ($qq) use ($kendaraanIds, $platList) {
                    if ($kendaraanIds->isNotEmpty()) {
                        $qq->whereIn('kendaraan_id', $kendaraanIds);
                    }
                    if ($platList->isNotEmpty()) {
                        $qq->orWhereIn('plat_no', $platList);
                    }
                });
            }, function ($q) {
                $q->whereRaw('1=0');
            })
            ->latest()
            ->paginate(20);

        return view('student.violations.index', compact('violations'));
    }
}
