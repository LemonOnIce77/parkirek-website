<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Middleware\AutoLogoutOnAuthPages;

/*
|--------------------------------------------------------------------------
| ADMIN (Web Controllers)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ScanController as AdminScanController;
use App\Http\Controllers\Admin\ScanLogController as AdminScanLogController;
use App\Http\Controllers\Admin\ViolationController as AdminViolationController;

/*
|--------------------------------------------------------------------------
| STUDENT (Web Controllers)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Student\KendaraanController as StudentKendaraanController;
use App\Http\Controllers\Student\ScanLogController as StudentScanLogController;
use App\Http\Controllers\Student\ViolationController as StudentViolationController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;

/*
|--------------------------------------------------------------------------
| INTERNAL API (AJAX - session cookie)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\InternalApi\Admin\ScanController as AdminScanApiController;
use App\Http\Controllers\InternalApi\Admin\ScanLogController as AdminScanLogApiController;
use App\Http\Controllers\InternalApi\Admin\KendaraanLookupController as AdminKendaraanLookupApiController;

use App\Http\Controllers\InternalApi\Mahasiswa\ScanLogController as MahasiswaScanLogApiController;
use App\Http\Controllers\InternalApi\Mahasiswa\ViolationController as MahasiswaViolationApiController;

/*
|--------------------------------------------------------------------------
| LANDING
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('landing.index'))->name('landing');

/*
|--------------------------------------------------------------------------
| PUBLIC: QR SCAN (NO AUTH)
|--------------------------------------------------------------------------
*/
Route::get('/v/{token}', [StudentKendaraanController::class, 'scan'])
    ->name('vehicle.scan');

/*
|--------------------------------------------------------------------------
| AUTH PAGES (guest only) + AUTO LOGOUT + NO CACHE (anti 419)
|--------------------------------------------------------------------------
| Tujuan:
| - user masih login pun kalau buka halaman login -> auto logout + CSRF fresh
| - tidak kena 419 Page Expired karena tab/back cache
|--------------------------------------------------------------------------
*/
Route::middleware([AutoLogoutOnAuthPages::class, 'guest'])->group(function () {

    // Student auth pages (VIEW + Register via Controller)
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/login', fn () => view('student.auth.login'))->name('auth.login');

        // ✅ register pakai controller (hapus redundansi view-only register)
        Route::get('/register', [RegisterController::class, 'show'])->name('auth.register');

        Route::get('/forgot-password', fn () => view('student.auth.forgot-password'))->name('auth.password.request');
        Route::get('/verify-code', fn () => view('student.auth.verify-code'))->name('auth.verify.code');
    });

    // Admin auth pages
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/login', fn () => view('admin.auth.login'))->name('auth.login');
        Route::get('/register', fn () => view('admin.auth.register'))->name('auth.register');
    });

    // Default Laravel route('login') -> student login
    Route::get('/login', fn () => redirect()->route('student.auth.login'))->name('login');
});

/*
|--------------------------------------------------------------------------
| AUTH ACTIONS (session)
|--------------------------------------------------------------------------
*/
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ✅ register submit (form action="{{ route('register.attempt') }}")
Route::post('/register', [RegisterController::class, 'register'])->name('register.attempt');

/*
|--------------------------------------------------------------------------
| ADMIN WEB - protected
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/scan', [AdminScanController::class, 'index'])->name('scan');
        Route::post('/scan/masuk', [AdminScanController::class, 'scanMasuk'])->name('scan.masuk');
        Route::post('/scan/keluar', [AdminScanController::class, 'scanKeluar'])->name('scan.keluar');

        // Entry/Exit Logs
        Route::get('/vehicle-logs', [AdminScanLogController::class, 'index'])->name('vehicle_logs');

        // alias lama
        Route::get('/scan-logs', fn () => redirect()->route('admin.vehicle_logs'))->name('scan_logs');

        // Violations
        Route::get('/violations', [AdminViolationController::class, 'index'])->name('violations');
        Route::post('/violations', [AdminViolationController::class, 'store'])->name('violations.store');
        Route::patch('/violations/{pelanggaran}/status', [AdminViolationController::class, 'updateStatus'])->name('violations.status');

        // Optional
        Route::get('/statistics', fn () => redirect()->route('admin.dashboard'))->name('statistics');
        Route::get('/locations', fn () => view('admin.locations'))->name('locations');
    });

/*
|--------------------------------------------------------------------------
| STUDENT WEB - protected
|--------------------------------------------------------------------------
*/
Route::prefix('student')
    ->name('student.')
    ->middleware(['auth', 'role:student'])
    ->group(function () {

        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

        Route::get('/vehicles', [StudentKendaraanController::class, 'index'])->name('vehicles.index');
        Route::get('/vehicles/create', [StudentKendaraanController::class, 'create'])->name('vehicles.create');
        Route::post('/vehicles', [StudentKendaraanController::class, 'store'])->name('vehicles.store');

        Route::get('/vehicles/{kendaraan}/edit', [StudentKendaraanController::class, 'edit'])->name('vehicles.edit');
        Route::put('/vehicles/{kendaraan}', [StudentKendaraanController::class, 'update'])->name('vehicles.update');
        Route::delete('/vehicles/{kendaraan}', [StudentKendaraanController::class, 'destroy'])->name('vehicles.destroy');

        Route::get('/vehicles/{kendaraan}/qr', [StudentKendaraanController::class, 'qr'])->name('vehicles.qr');

        Route::get('/violations', [StudentViolationController::class, 'index'])->name('violations.index');
        Route::get('/scan-logs', [StudentScanLogController::class, 'index'])->name('scan_logs.index');

        Route::get('/profile', fn () => view('student.profile.index'))->name('profile');
    });

/*
|--------------------------------------------------------------------------
| INTERNAL API (AJAX) - protected
|--------------------------------------------------------------------------
*/
Route::prefix('internal-api')
    ->middleware(['web', 'auth'])
    ->group(function () {

        Route::prefix('admin')
            ->middleware(['role:admin'])
            ->group(function () {
                Route::post('/scan/masuk', [AdminScanApiController::class, 'masuk']);
                Route::post('/scan/keluar', [AdminScanApiController::class, 'keluar']);
                Route::get('/scan-logs', [AdminScanLogApiController::class, 'index']);
                Route::get('/kendaraan/lookup', [AdminKendaraanLookupApiController::class, 'lookup']);
            });

        Route::prefix('mahasiswa')
            ->middleware(['role:student'])
            ->group(function () {
                Route::get('/scan-logs', [MahasiswaScanLogApiController::class, 'index']);
                Route::get('/violations', [MahasiswaViolationApiController::class, 'index']);
            });
    });
