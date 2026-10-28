@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-3 px-md-5 pt-4">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white border border-light-subtle shadow-sm mb-2">
                <span class="status-dot success"></span>
                <span class="small fw-semibold text-secondary">{{ now()->translatedFormat('l, d F Y') }} • Campus Gate Active</span>
            </div>
            <h1 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.02em;">Hi, {{ $studentName }} 👋</h1>
            <p class="text-muted mb-0">Here’s your vehicle activity and campus parking status today.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('student.vehicles.create') }}" class="btn btn-maroon">
                <i class="bi bi-plus-circle"></i> Add Vehicle
            </a>
            <a href="{{ route('student.vehicles.index') }}" class="btn btn-outline-maroon">
                <i class="bi bi-car-front"></i> My Vehicles
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="card dash-card h-100 p-2">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase tracking-wider mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Registered Vehicles</div>
                        <div class="display-5 fw-bold text-dark mb-0" style="letter-spacing: -0.03em;">{{ $totalVehicles }}</div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill mt-2">Active Passes</span>
                    </div>
                    <div class="dash-icon bg-maroon-soft text-maroon">
                        <i class="bi bi-car-front-fill"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                    <a href="{{ route('student.vehicles.index') }}" class="dash-link">
                        Manage vehicles <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card dash-card h-100 p-2">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase tracking-wider mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Recorded Violations</div>
                        <div class="display-5 fw-bold text-dark mb-0" style="letter-spacing: -0.03em;">{{ $totalViolations }}</div>
                        <span class="badge {{ $totalViolations > 0 ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' }} rounded-pill mt-2">
                            {{ $totalViolations > 0 ? 'Requires Attention' : 'Clean Record' }}
                        </span>
                    </div>
                    <div class="dash-icon bg-warning-soft text-warning">
                        <i class="bi bi-shield-exclamation"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                    <a href="{{ route('student.violations.index') }}" class="dash-link">
                        View violations <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Logbook --}}
    <div class="card dash-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-maroon"></i> Gate Activity Logbook
                    </h4>
                    <p class="text-muted small mb-0">Contactless entry/exit tracking registered per vehicle.</p>
                </div>
            </div>

            @if (count($logbookTabs) === 0)
                <div class="empty-state">
                    <div class="empty-icon"><i class="bi bi-qr-code-scan"></i></div>
                    <h5 class="fw-bold text-dark">No Registered Vehicles</h5>
                    <p class="text-muted small mb-3">Add your first vehicle with plate & STNK to start tracking gate logs.</p>
                    <a href="{{ route('student.vehicles.create') }}" class="btn btn-maroon">
                        <i class="bi bi-plus-circle me-1"></i> Add Vehicle Now
                    </a>
                </div>
            @else
                {{-- Tabs --}}
                <ul class="nav nav-pills dash-tabs mb-4" role="tablist">
                    @foreach ($logbookTabs as $idx => $tab)
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link {{ $idx === 0 ? 'active' : '' }}"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-vehicle-{{ $tab['kendaraan']->id }}"
                                type="button"
                                role="tab"
                            >
                                <i class="bi {{ $tab['kendaraan']->jenis_kendaraan === 'motor' ? 'bi-bicycle' : 'bi-car-front' }}"></i>
                                <span class="plate">{{ $tab['kendaraan']->plat_no }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>

                {{-- Content --}}
                <div class="tab-content">
                    @foreach ($logbookTabs as $idx => $tab)
                        @php($kendaraan = $tab['kendaraan'])
                        @php($grouped = $tab['grouped'])

                        <div
                            class="tab-pane fade {{ $idx === 0 ? 'show active' : '' }}"
                            id="tab-vehicle-{{ $kendaraan->id }}"
                            role="tabpanel"
                        >
                            @forelse($grouped as $dayLabel => $items)
                                <div class="log-day">
                                    <div class="log-day-head">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1">
                                                <i class="bi bi-calendar3 me-1"></i> {{ $dayLabel }}
                                            </span>
                                            <span class="text-muted small fw-medium">
                                                {{ \Carbon\Carbon::parse($items->first()['time'])->format('d F Y') }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="log-list">
                                        @foreach ($items as $item)
                                            <div class="log-item">
                                                <span class="time-pill">
                                                    <i class="bi bi-clock me-1 text-muted"></i>
                                                    {{ \Carbon\Carbon::parse($item['time'])->format('H:i') }}
                                                </span>

                                                <span class="log-text">{{ $item['text'] }}</span>

                                                <span class="status-dot {{ $item['dot'] }}" title="Status"></span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 text-secondary opacity-50 d-block mb-2"></i>
                                    <span>No gate activity recorded for this vehicle yet.</span>
                                </div>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
