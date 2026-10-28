<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mahasiswa\StoreKendaraanRequest;
use App\Http\Requests\Mahasiswa\UpdateKendaraanRequest;
use App\Models\Kendaraan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Response;

class KendaraanController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        $kendaraans = Kendaraan::query()
            ->where('user_id', $userId)
            ->latest()
            ->paginate(10);

        return view('student.vehicles.show', compact('kendaraans'));
    }

    public function create(): View
    {
        return view('student.vehicles.create');
    }

    public function store(StoreKendaraanRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Ownership (mahasiswa hanya buat untuk dirinya)
        $data['user_id'] = $request->user()->id;

        // Upload STNK photo (optional)
        if ($request->hasFile('stnk_photo')) {
            $file = $request->file('stnk_photo');
            $filename = (string) Str::uuid() . '.' . $file->getClientOriginalExtension();
            $data['stnk_photo_path'] = $file->storeAs('stnk', $filename, 'public'); // stnk/xxx.jpg
        }

        // QR token dibuat oleh sistem (lebih aman), jangan dari input user
        $data['qr_token'] = (string) Str::uuid();

        Kendaraan::create($data);

        return redirect()
            ->route('student.vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit(Kendaraan $kendaraan): View
    {
        $this->ensureOwner($kendaraan);
        return view('student.vehicles.edit', compact('kendaraan'));
    }

    public function update(UpdateKendaraanRequest $request, Kendaraan $kendaraan): RedirectResponse
    {
        $this->ensureOwner($kendaraan);

        $data = $request->validated();

        // Jika upload foto baru → hapus foto lama, lalu simpan yang baru
        if ($request->hasFile('stnk_photo')) {
            if ($kendaraan->stnk_photo_path) {
                Storage::disk('public')->delete($kendaraan->stnk_photo_path);
            }

            $file = $request->file('stnk_photo');
            $filename = (string) Str::uuid() . '.' . $file->getClientOriginalExtension();
            $data['stnk_photo_path'] = $file->storeAs('stnk', $filename, 'public');
        }

        // qr_token jangan berubah saat update
        unset($data['qr_token']);

        $kendaraan->update($data);

        return redirect()
            ->route('student.vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function destroy(Kendaraan $kendaraan): RedirectResponse
    {
        $this->ensureOwner($kendaraan);

        // hapus file STNK jika ada
        if ($kendaraan->stnk_photo_path) {
            Storage::disk('public')->delete($kendaraan->stnk_photo_path);
        }

        $kendaraan->delete();
        return redirect()
            ->route('student.vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function qr(Kendaraan $kendaraan): Response
    {
        $this->ensureOwner($kendaraan);

        $url = route('vehicle.scan', $kendaraan->qr_token);

        $svg = QrCode::format('svg')
            ->size(260)
            ->margin(1)
            ->generate($url);

        return response($svg, 200)->header('Content-Type', 'image/svg+xml');
    }

    public function scan(string $token): View
    {
        $kendaraan = Kendaraan::query()
            ->where('qr_token', $token)
            ->firstOrFail();

        return view('vehicle.scan', compact('kendaraan'));
    }


    /**
     * Authorization: mahasiswa hanya boleh akses kendaraan miliknya.
     */
    private function ensureOwner(Kendaraan $kendaraan): void
    {
        abort_unless(
            $kendaraan->user_id === Auth::id(),
            403,
            'Tidak diizinkan mengakses kendaraan ini.'
        );
    }
}
