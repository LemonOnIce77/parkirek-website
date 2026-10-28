@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-5 pt-3">

    <!-- Header -->
    <div class="mb-4">
        <h1 class="fw-bold mb-1 d-flex align-items-center gap-2">
            Parking Violations
            <i class="bi bi-exclamation-circle text-maroon opacity-75"></i>
        </h1>
        <p class="text-muted fs-6 mb-0">Your violation history and status</p>
    </div>

    <div class="d-grid gap-3">

        @forelse ($violations as $v)
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3 align-items-center">

                        <!-- Violation -->
                        <div class="col-12 col-md-4">
                            <div class="d-flex gap-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center"
                                     style="width:42px;height:42px;background:rgba(123,30,43,.1);color:#7b1e2b;">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>

                                <div>
                                    <div class="text-muted small">Violation</div>
                                    <div class="fw-bold text-maroon">
                                        {{ $v->jenis_pelanggaran }}
                                    </div>
                                    <div class="text-muted small mt-1">
                                        Plate: <span class="fw-semibold">{{ $v->plat_no }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="col-6 col-md-2">
                            <div class="text-muted small">
                                <i class="bi bi-calendar-event me-1"></i>Date
                            </div>
                            <div class="fw-semibold">
                                {{ $v->created_at->format('d-m-Y') }}
                            </div>
                        </div>

                        <!-- Time -->
                        <div class="col-6 col-md-2">
                            <div class="text-muted small">
                                <i class="bi bi-clock me-1"></i>Reported
                            </div>
                            <div class="fw-semibold">
                                {{ $v->created_at->format('H:i') }}
                            </div>
                        </div>

                        <!-- Fine -->
                        <div class="col-6 col-md-2">
                            <div class="text-muted small">
                                <i class="bi bi-cash-coin me-1"></i>Fine
                            </div>
                            <div class="fw-semibold">
                                {{ $v->denda ? 'Rp '.number_format($v->denda,0,',','.') : '-' }}
                            </div>
                        </div>

                        <!-- Status & Action -->
                        <div class="col-6 col-md-2 text-md-end">
                            @php
                                $statusMap = [
                                    'pending' => 'warning',
                                    'resolved' => 'success',
                                    'paid' => 'primary',
                                    'rejected' => 'danger',
                                ];
                            @endphp

                            <span class="badge rounded-pill text-bg-{{ $statusMap[$v->status] ?? 'secondary' }}">
                                {{ ucfirst($v->status) }}
                            </span>

                            <div class="mt-2">
                                <button
                                    class="btn btn-sm btn-outline-maroon w-100 w-md-auto"
                                    data-bs-toggle="modal"
                                    data-bs-target="#violationModal{{ $v->id }}"
                                >
                                    Detail
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- DETAIL MODAL -->
            <div class="modal fade" id="violationModal{{ $v->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content rounded-4">
                        <div class="modal-header border-0">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-exclamation-circle text-maroon me-2"></i>
                                Violation Detail
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body pt-0">
                            <div class="mb-3">
                                <div class="text-muted small">Violation</div>
                                <div class="fw-semibold">{{ $v->jenis_pelanggaran }}</div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Plate Number</div>
                                <div class="fw-semibold">{{ $v->plat_no }}</div>
                            </div>

                            @if ($v->deskripsi)
                                <div class="mb-3">
                                    <div class="text-muted small">Description</div>
                                    <div>{{ $v->deskripsi }}</div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <div class="text-muted small">Fine</div>
                                <div class="fw-semibold">
                                    {{ $v->denda ? 'Rp '.number_format($v->denda,0,',','.') : '-' }}
                                </div>
                            </div>

                            @if ($v->photo_path)
                                <div class="mb-3">
                                    <div class="text-muted small mb-1">Evidence Photo</div>
                                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden border">
                                        <img
                                            src="{{ asset('storage/'.$v->photo_path) }}"
                                            class="w-100 h-100"
                                            style="object-fit:cover;"
                                        >
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-check-circle fs-1 d-block mb-2 opacity-50"></i>
                <div class="fw-semibold">No violations found</div>
                <div class="small">You have no parking violations 🎉</div>
            </div>
        @endforelse

    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $violations->links() }}
    </div>

</div>
@endsection
