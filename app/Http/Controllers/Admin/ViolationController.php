<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreViolationRequest;
use App\Models\Kendaraan;
use App\Models\Pelanggaran;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ViolationController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today();

        $status = strtoupper((string) $request->query('status', '')); // OPEN/CLOSED/''
        $platNo = (string) $request->query('plat_no', '');

        // ===== Cards =====
        $activeOpen = Pelanggaran::query()
            ->where('status', 'OPEN')
            ->count();

        $unpaidTotal = (int) Pelanggaran::query()
            ->where('status', 'OPEN')
            ->sum('denda');

        // kalau tabel kamu punya updated_at, ini aman.
        // kalau belum pakai timestamps, ganti ke created_at.
        $resolvedToday = Pelanggaran::query()
            ->where('status', 'CLOSED')
            ->whereDate('updated_at', $today)
            ->count();

        // ===== Table =====
        $query = Pelanggaran::query()
            ->with([
                'kendaraan.user',  // owner
                'creator',         // admin pelapor
            ])
            ->latest();

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($platNo !== '') {
            $query->where('plat_no', 'like', "%{$platNo}%");
        }

        $violations = $query->paginate(20)->withQueryString();

        // ===== Kendaraan list (untuk dropdown modal) =====
        // Ambil ringan aja, lalu owner pakai relasi user
        $kendaraans = Kendaraan::query()
            ->with(['user:id,nama,email']) // hindari kolom "name"
            ->orderBy('plat_no')
            ->get(['id', 'user_id', 'plat_no', 'stnk_number', 'jenis_kendaraan']);

        return view('admin.violations', [
            'violations' => $violations,
            'status'     => $status,
            'platNo'     => $platNo,
            'cards'      => [
                'active_open'    => $activeOpen,
                'unpaid_total'   => $unpaidTotal,
                'resolved_today' => $resolvedToday,
            ],
            'kendaraans' => $kendaraans,
        ]);
    }

    public function store(StoreViolationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $adminId = $request->user()->id;

        // kendaraan wajib dipilih dari dropdown
        $kendaraan = Kendaraan::query()
            ->with('user')
            ->findOrFail($data['kendaraan_id']);

        $photoPath = null;
        if ($request->hasFile('foto')) {
            $photoPath = $request->file('foto')->store('violations', 'public');
        }

        DB::transaction(function () use ($data, $adminId, $kendaraan, $photoPath) {
            Pelanggaran::create([
                'kendaraan_id'      => $kendaraan->id,
                'created_by'        => $adminId,

                // auto dari kendaraan
                'plat_no'           => strtoupper(trim($kendaraan->plat_no)),

                // input admin
                'jenis_pelanggaran' => $data['jenis_pelanggaran'],
                'deskripsi'         => $data['deskripsi'] ?? null,
                'denda'             => $data['denda'] ?? null,
                'photo_path'        => $photoPath,

                'status'            => 'OPEN',
            ]);
        });

        return redirect()
            ->route('admin.violations')
            ->with('success', 'Pelanggaran berhasil dibuat.');
    }

    public function updateStatus(Request $request, Pelanggaran $pelanggaran): RedirectResponse
    {
        abort_unless(($request->user()->role ?? null) === 'admin', 403);

        $validated = $request->validate([
            'status' => ['required', 'string', 'max:20', Rule::in(['OPEN', 'CLOSED'])],
        ], [
            'status.in' => 'Status harus OPEN atau CLOSED.',
        ]);

        $pelanggaran->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Status pelanggaran berhasil diperbarui.');
    }
}
