@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-5 pt-3">

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="fw-bold mb-1 d-flex align-items-center gap-2">
                My Vehicles <i class="bi bi-car-front-fill text-maroon"></i>
            </h1>
            <p class="text-muted fs-6 mb-0">Manage your registered vehicles</p>
        </div>

        <a href="{{ route('student.vehicles.create') }}" class="btn btn-outline-maroon">
            <i class="bi bi-plus-circle me-1"></i> Add Vehicle
        </a>
    </div>

    <div class="row g-4">

        <!-- LEFT -->
        <div class="col-12 col-lg-7">
            <div class="d-grid gap-3">

                @forelse($kendaraans as $kendaraan)
                    <div
                        role="button"
                        tabindex="0"
                        class="vcard js-vehicle"
                        data-jenis="{{ $kendaraan->jenis_kendaraan }}"
                        data-plat="{{ $kendaraan->plat_no }}"
                        data-stnk="{{ $kendaraan->stnk_number }}"
                        data-qr-image="{{ route('student.vehicles.qr', $kendaraan->id) }}"
                    >
                        <div class="vcard-inner">

                            <!-- Left content -->
                            <div class="v-left">
                                <div class="v-topline">
                                    <span class="v-icon">
                                        <i class="bi {{ $kendaraan->jenis_kendaraan === 'motor' ? 'bi-bicycle' : 'bi-car-front' }}"></i>
                                    </span>

                                    <div class="v-titlewrap">
                                        <div class="v-title">
                                            {{ $kendaraan->jenis_kendaraan === 'motor' ? 'Motorcycle' : 'Car' }}
                                        </div>
                                        <div class="v-sub text-muted">
                                            {{ strtoupper($kendaraan->jenis_kendaraan) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="v-plate">
                                    {{ $kendaraan->plat_no }}
                                </div>
                            </div>

                            <!-- Right content -->
                            <div class="v-right">
                                <div class="v-actions">
                                    <a
                                        href="{{ route('student.vehicles.edit', $kendaraan->id) }}"
                                        class="icon-btn"
                                        title="Edit"
                                        onclick="event.stopPropagation()"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <button
                                        class="icon-btn danger"
                                        type="button"
                                        title="Delete"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteVehicleModal"
                                        data-vehicle-name="{{ $kendaraan->jenis_kendaraan === 'motor' ? 'Motorcycle' : 'Car' }}"
                                        data-vehicle-plate="{{ $kendaraan->plat_no }}"
                                        data-delete-action="{{ route('student.vehicles.destroy', $kendaraan->id) }}"
                                        onclick="event.stopPropagation()"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile status -->
                        <div class="v-status-mobile d-md-none">
                            <span class="text-muted small">Parking</span>
                            <span class="badge badge-soft badge-notparked">Not Parked</span>
                        </div>
                    </div>
                @empty
                    <div class="text-muted">Belum ada kendaraan.</div>
                @endforelse

            </div>

            <div class="mt-3">
                {{ $kendaraans->links() }}
            </div>
        </div>

        <!-- RIGHT -->
        <div class="col-12 col-lg-5">
            <div class="sidecard sticky-lg-top" style="top: 88px;">

                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Vehicle Info</h5>
                        <p class="text-muted mb-0 small">Select a vehicle to preview its QR.</p>
                    </div>
                    <i class="bi bi-qr-code text-maroon fs-4"></i>
                </div>

                <div class="side-qrwrap">
                    <img id="sideQrImg" src="" alt="QR" class="img-fluid side-qr" style="display:none;">
                    <div id="sideQrPlaceholder" class="side-qrph text-muted small">
                        QR preview will appear here
                    </div>
                </div>

                <div class="side-info">
                    <div class="side-row">
                        <span class="side-k text-muted small">Plate</span>
                        <span class="side-v fw-semibold" id="sidePlat">—</span>
                    </div>
                    <div class="side-row">
                        <span class="side-k text-muted small">Type</span>
                        <span class="side-v fw-semibold" id="sideJenis">—</span>
                    </div>
                    <div class="side-row">
                        <span class="side-k text-muted small">STNK Number</span>
                        <span class="side-v fw-semibold" id="sideStnk">—</span>
                    </div>
                </div>

                <div class="mt-3 d-grid d-sm-flex gap-2">
                    <button
                        id="btnOpenModal"
                        type="button"
                        class="btn btn-maroon flex-fill"
                        disabled
                        data-bs-toggle="modal"
                        data-bs-target="#qrVehicleModal"
                    >
                        <i class="bi bi-qr-code-scan me-1"></i> Open QR
                    </button>
                </div>

                <div class="mt-2 text-muted small">
                    *QR is for gate officer scanning. Scan link is hidden for security.
                </div>

            </div>
        </div>

    </div>
</div>

<!-- QR MODAL -->
<div class="modal fade" id="qrVehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-qr-code me-2 text-maroon"></i> Vehicle QR
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body pt-0">
                <div class="text-center">
                    <img id="qrImg" src="" alt="QR" class="img-fluid" style="max-width: 240px;">
                </div>

                <div class="mt-3 p-3 rounded-3" style="background:#f8f9fa; border:1px solid #eee;">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Plate</span>
                        <span class="fw-semibold" id="qrPlat">-</span>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="text-muted small">Type</span>
                        <span class="fw-semibold" id="qrJenis">-</span>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="text-muted small">STNK</span>
                        <span class="fw-semibold" id="qrStnk">-</span>
                    </div>
                </div>

                <div class="mt-3 text-muted small">
                    *This QR is meant to be scanned by gate officers.
                </div>
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- DELETE MODAL -->
<div class="modal fade" id="deleteVehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-trash me-2"></i> Delete Vehicle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body pt-0">
                <p class="mb-2">Are you sure you want to delete this vehicle?</p>
                <div class="p-3 rounded-3" style="background:#f8f9fa; border:1px solid #eee;">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Vehicle</span>
                        <span class="fw-semibold" id="delVehicleName">—</span>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="text-muted small">Plate</span>
                        <span class="fw-semibold" id="delVehiclePlate">—</span>
                    </div>
                </div>
                <div class="text-muted small mt-3">
                    This action cannot be undone.
                </div>
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>

                <form id="deleteVehicleForm" method="POST" action="#">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* ===== Left card ===== */
.vcard{
    background:#fff;
    border:1px solid rgba(0,0,0,.06);
    border-radius:16px;
    padding:16px;
    box-shadow: 0 6px 18px rgba(0,0,0,.06);
    transition: .15s ease;
    cursor:pointer;
    outline: none;
}
.vcard:hover{
    transform: translateY(-1px);
    box-shadow: 0 14px 30px rgba(0,0,0,.10);
    border-color: rgba(123,30,43,.22);
}
.vcard.is-active{
    border-color: rgba(123,30,43,.55);
    box-shadow: 0 16px 34px rgba(123,30,43,.12);
}

.vcard-inner{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:14px;
}

.v-left{ min-width: 0; }
.v-topline{
    display:flex;
    align-items:center;
    gap:10px;
}
.v-icon{
    width:36px;height:36px;
    border-radius:12px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    background: rgba(123,30,43,.10);
    color:#7b1e2b;
    flex: 0 0 auto;
}
.v-titlewrap{ min-width:0; }
.v-title{
    font-weight:800;
    line-height:1.1;
}
.v-sub{
    font-size:.85rem;
    line-height:1.1;
}

.v-plate{
    margin-top:10px;
    font-weight:900;
    letter-spacing:.4px;
    color:#7b1e2b;
    font-size:1.10rem;
}

.v-right{
    display:flex;
    flex-direction:column;
    align-items:flex-end;
    gap:12px;
    flex: 0 0 auto;
}
.v-actions{
    display:flex;
    gap:8px;
}

.v-status-mobile{
    margin-top:12px;
    padding-top:12px;
    border-top: 1px dashed rgba(0,0,0,.10);
    display:flex;
    justify-content:space-between;
    align-items:center;
}

/* ===== Right side ===== */
.sidecard{
    background:#fff;
    border:1px solid rgba(0,0,0,.06);
    border-radius:16px;
    padding:16px;
    box-shadow: 0 10px 26px rgba(0,0,0,.06);
}
.side-qrwrap{
    border:1px dashed rgba(123,30,43,.25);
    border-radius:14px;
    padding:14px;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height: 200px;
    background: #fff;
}
.side-qr{
    max-width: 220px;
}
.side-qrph{
    text-align:center;
}
.side-info{
    margin-top:14px;
    background:#f8f9fa;
    border:1px solid #eee;
    border-radius:14px;
    padding:12px;
}
.side-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    padding:6px 0;
}
.side-row + .side-row{
    border-top: 1px dashed rgba(0,0,0,.08);
}
.side-v{ text-align:right; }

/* Small screens spacing */
@media (max-width: 576px){
    .container-fluid{ padding-left: 16px !important; padding-right: 16px !important; }
    .vcard{ padding:14px; }
    .side-qrwrap{ min-height: 180px; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.js-vehicle');
    const sideQrImg = document.getElementById('sideQrImg');
    const sideQrPlaceholder = document.getElementById('sideQrPlaceholder');

    function setActive(el){
        cards.forEach(c => c.classList.remove('is-active'));
        if (el) el.classList.add('is-active');
    }

    function updateSide(el){
        const jenis = el.getAttribute('data-jenis') || '';
        const plat  = el.getAttribute('data-plat') || '';
        const stnk  = el.getAttribute('data-stnk') || '';
        const qrImg = el.getAttribute('data-qr-image') || '';

        document.getElementById('sideJenis').textContent = (jenis || '—').toUpperCase();
        document.getElementById('sidePlat').textContent  = plat || '—';
        document.getElementById('sideStnk').textContent  = stnk || '—';

        if (qrImg) {
            sideQrImg.src = qrImg;
            sideQrImg.style.display = 'block';
            sideQrPlaceholder.style.display = 'none';
        } else {
            sideQrImg.src = '';
            sideQrImg.style.display = 'none';
            sideQrPlaceholder.style.display = 'block';
        }

        const btnModal = document.getElementById('btnOpenModal');
        btnModal.disabled = !qrImg;
        btnModal._selected = el;
    }

    cards.forEach(el => {
        el.addEventListener('click', () => { setActive(el); updateSide(el); });
        el.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                setActive(el); updateSide(el);
            }
        });
    });

    if (cards.length > 0) {
        setActive(cards[0]);
        updateSide(cards[0]);
    }

    // Modal fill
    const qrModal = document.getElementById('qrVehicleModal');
    if (qrModal) {
        qrModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            const source = (btn && btn.id === 'btnOpenModal' && btn._selected) ? btn._selected : btn;

            const jenis = source?.getAttribute('data-jenis') || '-';
            const plat  = source?.getAttribute('data-plat') || '-';
            const stnk  = source?.getAttribute('data-stnk') || '-';
            const qrImg = source?.getAttribute('data-qr-image') || '';

            document.getElementById('qrJenis').textContent = jenis.toUpperCase();
            document.getElementById('qrPlat').textContent  = plat;
            document.getElementById('qrStnk').textContent  = stnk;

            document.getElementById('qrImg').src = qrImg;
        });
    }

    // Delete modal fill
    const delModal = document.getElementById('deleteVehicleModal');
    if (delModal) {
        delModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            const name = btn?.getAttribute('data-vehicle-name') || '—';
            const plate = btn?.getAttribute('data-vehicle-plate') || '—';
            const action = btn?.getAttribute('data-delete-action') || '#';

            document.getElementById('delVehicleName').textContent = name;
            document.getElementById('delVehiclePlate').textContent = plate;

            document.getElementById('deleteVehicleForm').setAttribute('action', action);
        });
    }
});
</script>
@endsection
