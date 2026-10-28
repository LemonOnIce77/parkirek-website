<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\ParkingRecord;
use App\Models\Pelanggaran;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        // Summary cards
        $totalKendaraanTerdaftar = Kendaraan::query()->count();

        $totalSedangParkir = ParkingRecord::query()
            ->where('status', 'ACTIVE')
            ->count();

        // Vehicles IN = jam_masuk hari ini
        $vehiclesInToday = ParkingRecord::query()
            ->whereDate('jam_masuk', $today)
            ->count();

        // Vehicles OUT = jam_keluar hari ini
        $vehiclesOutToday = ParkingRecord::query()
            ->whereNotNull('jam_keluar')
            ->whereDate('jam_keluar', $today)
            ->count();

        $totalPelanggaranOpen = Pelanggaran::query()
            ->where('status', 'OPEN')
            ->count();

        // Tabel: Kendaraan Sedang Parkir
        $kendaraanSedangParkir = ParkingRecord::query()
            ->where('status', 'ACTIVE')
            ->whereNull('jam_keluar')
            ->with(['admin'])
            ->orderByDesc('jam_masuk')
            ->limit(20)
            ->get([
                'id',
                'plat_snapshot',
                'stnk_snapshot',
                'jam_masuk',
                'scanned_by',
                'status',
            ]);

        // Recent Activity (limit 10): berdasarkan event terakhir (IN/OUT)
        $scanTerbaru = ParkingRecord::query()
            ->orderByRaw(
                "GREATEST(COALESCE(jam_keluar,'1970-01-01'), COALESCE(jam_masuk,'1970-01-01')) DESC"
            )
            ->limit(10)
            ->get([
                'id',
                'plat_snapshot',
                'status',
                'jam_masuk',
                'jam_keluar',
            ]);

        // Tabel: Pelanggaran Terbaru (limit 10)
        $pelanggaranTerbaru = Pelanggaran::query()
            ->orderByDesc('created_at')
            ->limit(10)
            ->get([
                'id',
                'plat_no',
                'jenis_pelanggaran',
                'status',
                'denda',
                'created_at',
            ]);

        /**
         * =========================
         * TRAFFIC CHART DATA (REAL)
         * =========================
         * Traffic = total kendaraan masuk (jam_masuk)
         */

        // WEEK (7 hari terakhir) -> per hari
        $startWeek = Carbon::today()->subDays(6);
        $weekRows = ParkingRecord::query()
            ->selectRaw('DATE(jam_masuk) as d, COUNT(*) as total')
            ->whereDate('jam_masuk', '>=', $startWeek)
            ->whereDate('jam_masuk', '<=', $today)
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        // buat map tanggal->jumlah, biar hari yang kosong tetap 0
        $weekMap = $weekRows->pluck('total', 'd')->toArray();
        $weekLabels = [];
        $weekData = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startWeek->copy()->addDays($i);
            $key = $date->toDateString(); // YYYY-MM-DD
            $weekLabels[] = $date->format('D'); // Mon, Tue, ...
            $weekData[] = (int)($weekMap[$key] ?? 0);
        }

        // MONTH (bulan ini) -> per minggu (Week 1-5)
        $startMonth = Carbon::now()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();

        $monthRows = ParkingRecord::query()
            ->selectRaw('WEEK(jam_masuk, 1) as w, COUNT(*) as total')
            ->whereBetween('jam_masuk', [$startMonth->copy()->startOfDay(), $endMonth->copy()->endOfDay()])
            ->groupBy('w')
            ->orderBy('w')
            ->get();

        $monthMap = $monthRows->pluck('total', 'w')->toArray();

        // hitung range week number untuk bulan ini (ISO week)
        $startWeekNum = (int)$startMonth->format('W');
        $endWeekNum = (int)$endMonth->format('W');

        // handle kasus Des/Jan yang week number lompat (mis. 52 -> 1)
        $monthLabels = [];
        $monthData = [];
        if ($endWeekNum >= $startWeekNum) {
            $idx = 1;
            for ($w = $startWeekNum; $w <= $endWeekNum; $w++) {
                $monthLabels[] = 'Week ' . $idx++;
                $monthData[] = (int)($monthMap[$w] ?? 0);
            }
        } else {
            // fallback aman: pakai Week 1-5 berbasis tanggal (lebih stabil)
            $weeks = [];
            $cursor = $startMonth->copy();
            while ($cursor->lte($endMonth)) {
                $weeks[] = $cursor->copy();
                $cursor->addWeek();
            }
            foreach ($weeks as $i => $wkStart) {
                $wkEnd = $wkStart->copy()->addDays(6)->endOfDay();
                $count = ParkingRecord::query()
                    ->whereBetween('jam_masuk', [$wkStart->copy()->startOfDay(), $wkEnd])
                    ->count();

                $monthLabels[] = 'Week ' . ($i + 1);
                $monthData[] = (int)$count;
            }
        }

        // YEAR (tahun ini) -> per bulan
        $startYear = Carbon::now()->startOfYear();
        $endYear = Carbon::now()->endOfYear();

        $yearRows = ParkingRecord::query()
            ->selectRaw('MONTH(jam_masuk) as m, COUNT(*) as total')
            ->whereBetween('jam_masuk', [$startYear->copy()->startOfDay(), $endYear->copy()->endOfDay()])
            ->groupBy('m')
            ->orderBy('m')
            ->get();

        $yearMap = $yearRows->pluck('total', 'm')->toArray();

        $yearLabels = [];
        $yearData = [];
        for ($m = 1; $m <= 12; $m++) {
            $yearLabels[] = Carbon::create(null, $m, 1)->format('M'); // Jan, Feb, ...
            $yearData[] = (int)($yearMap[$m] ?? 0);
        }

        return view('admin.dashboard', [
            'summary' => [
                'total_kendaraan_terdaftar' => $totalKendaraanTerdaftar,
                'total_sedang_parkir'       => $totalSedangParkir,
                'vehicles_in_today'         => $vehiclesInToday,
                'vehicles_out_today'        => $vehiclesOutToday,
                'total_pelanggaran_open'    => $totalPelanggaranOpen,
            ],
            'kendaraanSedangParkir' => $kendaraanSedangParkir,
            'scanTerbaru'           => $scanTerbaru,
            'pelanggaranTerbaru'    => $pelanggaranTerbaru,
            'today'                 => $today,

            // chart datasets (ready untuk dropdown)
            'trafficChart' => [
                'week' => ['labels' => $weekLabels, 'data' => $weekData],
                'month' => ['labels' => $monthLabels, 'data' => $monthData],
                'year' => ['labels' => $yearLabels, 'data' => $yearData],
            ],
        ]);
    }
}
