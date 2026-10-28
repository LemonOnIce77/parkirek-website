@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-5 pt-3">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-6 col-xl-5">

            <div class="vehicle-form-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="fw-bold mb-0">Add New Vehicle</h4>
                    <i class="bi bi-pencil-square text-maroon"></i>
                </div>

                {{-- Server-side validation errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <div class="fw-semibold mb-1">Please fix the following:</div>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li class="small">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="vehicleCreateForm" method="POST" action="{{ route('student.vehicles.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- License Plate -->
                    <div class="mb-3">
                        <label class="form-label small text-muted">License plate number</label>
                        <input
                            type="text"
                            name="plat_no"
                            value="{{ old('plat_no') }}"
                            class="form-control form-soft @error('plat_no') is-invalid @enderror"
                            placeholder="ex. B 1234 XYZ"
                            autocomplete="off"
                        >
                        @error('plat_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Vehicle Type -->
                    <div class="mb-3">
                        <label class="form-label small text-muted">Vehicle Type</label>
                        <select
                            name="jenis_kendaraan"
                            class="form-select form-soft @error('jenis_kendaraan') is-invalid @enderror"
                        >
                            <option value="" disabled {{ old('jenis_kendaraan') ? '' : 'selected' }}>Select type</option>
                            <option value="mobil" {{ old('jenis_kendaraan') === 'mobil' ? 'selected' : '' }}>Car</option>
                            <option value="motor" {{ old('jenis_kendaraan') === 'motor' ? 'selected' : '' }}>Motorcycle</option>
                        </select>
                        @error('jenis_kendaraan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- STNK Number -->
                    <div class="mb-3">
                        <label class="form-label small text-muted">STNK Number</label>
                        <input
                            type="text"
                            name="stnk_number"
                            value="{{ old('stnk_number') }}"
                            class="form-control form-soft @error('stnk_number') is-invalid @enderror"
                            placeholder="ex. 1234567890"
                            autocomplete="off"
                        >
                        @error('stnk_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- STNK Photo (Drag & Drop + Preview + Client Validation) -->
                    <div class="mb-3">
                        <label class="form-label small text-muted">STNK Photo</label>

                        <div
                            id="stnkDropzone"
                            class="border border-2 rounded-4 p-4 text-center bg-light"
                            style="border-style:dashed;"
                            role="button"
                            tabindex="0"
                        >
                            <div class="mb-2">
                                <i class="bi bi-cloud-arrow-up fs-2 text-maroon"></i>
                            </div>
                            <div class="fw-semibold">Drag & drop file here</div>
                            <div class="text-muted small">or click to choose (JPG/PNG, max 5MB)</div>

                            <input
                                id="stnk_photo"
                                type="file"
                                name="stnk_photo"
                                accept="image/png,image/jpeg"
                                class="d-none"
                            >
                        </div>

                        <!-- Client error (before submit) -->
                        <div id="stnkClientError" class="alert alert-danger py-2 px-3 mt-3 d-none mb-0">
                            <small id="stnkClientErrorText"></small>
                        </div>

                        <!-- Preview -->
                        <div id="stnkPreviewWrap" class="mt-3 d-none">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                <div class="small text-muted">
                                    Selected: <span class="fw-semibold" id="stnkFileName">-</span>
                                    <span class="text-muted">•</span>
                                    <span class="small" id="stnkFileSize">-</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="stnkRemoveBtn">
                                    <i class="bi bi-x-circle me-1"></i> Remove
                                </button>
                            </div>

                            <div class="ratio ratio-16x9 rounded-4 overflow-hidden border bg-white">
                                <img id="stnkPreviewImg" src="" alt="STNK Preview" class="w-100 h-100" style="object-fit:cover;">
                            </div>

                            <!-- Progress (simulation for normal form submit) -->
                            <div class="mt-3 d-none" id="stnkProgressWrap">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Uploading...</span>
                                    <span id="stnkProgressPct">0%</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div id="stnkProgressBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                        @error('stnk_photo')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Note -->
                    <div class="alert alert-warning small mb-3">
                        <strong>Note:</strong> All vehicle information must match your STNK documents.
                        Photos will be verified by admin.
                    </div>

                    <!-- Buttons -->
                    <div class="d-grid gap-2">
                        <button id="btnSubmitVehicle" type="submit" class="btn btn-maroon w-100">
                            <i class="bi bi-check2-circle me-1"></i> Add vehicle
                        </button>

                        <a href="{{ route('student.vehicles.index') }}" class="btn btn-outline-secondary w-100">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropzone = document.getElementById('stnkDropzone');
    const input = document.getElementById('stnk_photo');

    const errBox = document.getElementById('stnkClientError');
    const errText = document.getElementById('stnkClientErrorText');

    const previewWrap = document.getElementById('stnkPreviewWrap');
    const fileNameEl = document.getElementById('stnkFileName');
    const fileSizeEl = document.getElementById('stnkFileSize');
    const imgEl = document.getElementById('stnkPreviewImg');
    const removeBtn = document.getElementById('stnkRemoveBtn');

    const progressWrap = document.getElementById('stnkProgressWrap');
    const progressBar = document.getElementById('stnkProgressBar');
    const progressPct = document.getElementById('stnkProgressPct');

    const form = document.getElementById('vehicleCreateForm');

    const MAX_MB = 5;
    const MAX_BYTES = MAX_MB * 1024 * 1024;
    const ALLOWED = ['image/jpeg', 'image/png'];

    function showError(msg) {
        errText.textContent = msg;
        errBox.classList.remove('d-none');
    }

    function clearError() {
        errText.textContent = '';
        errBox.classList.add('d-none');
    }

    function resetAll() {
        clearError();
        input.value = '';
        previewWrap.classList.add('d-none');
        imgEl.src = '';
        fileNameEl.textContent = '-';
        fileSizeEl.textContent = '-';

        // reset progress
        progressWrap.classList.add('d-none');
        progressBar.style.width = '0%';
        progressPct.textContent = '0%';
    }

    function humanSize(bytes) {
        const mb = bytes / (1024 * 1024);
        return mb.toFixed(2) + ' MB';
    }

    function setFile(file) {
        clearError();

        if (!file) {
            resetAll();
            return;
        }

        if (!ALLOWED.includes(file.type)) {
            resetAll();
            showError('File must be JPG or PNG.');
            return;
        }

        if (file.size > MAX_BYTES) {
            resetAll();
            showError('File is too large. Max size is 5MB.');
            return;
        }

        // Put file into input (DataTransfer trick)
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;

        previewWrap.classList.remove('d-none');
        fileNameEl.textContent = file.name;
        fileSizeEl.textContent = humanSize(file.size);

        const url = URL.createObjectURL(file);
        imgEl.src = url;
        imgEl.onload = () => URL.revokeObjectURL(url);
    }

    if (!dropzone || !input) return;

    // Click to open file picker
    dropzone.addEventListener('click', () => input.click());
    dropzone.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            input.click();
        }
    });

    // Change via picker
    input.addEventListener('change', () => {
        const file = input.files && input.files[0];
        setFile(file);
    });

    // Drag styles
    function setDragging(on) {
        if (on) {
            dropzone.classList.add('bg-white');
            dropzone.classList.add('border-danger'); // fallback bootstrap color
        } else {
            dropzone.classList.remove('bg-white');
            dropzone.classList.remove('border-danger');
        }
    }

    dropzone.addEventListener('dragenter', (e) => {
        e.preventDefault();
        setDragging(true);
    });

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        setDragging(true);
    });

    dropzone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        setDragging(false);
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        setDragging(false);

        const file = e.dataTransfer.files && e.dataTransfer.files[0];
        setFile(file);
    });

    // Remove
    removeBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        resetAll();
    });

    // Progress simulation on normal submit
    form?.addEventListener('submit', () => {
        const hasFile = input.files && input.files.length > 0;
        if (!hasFile) return;

        progressWrap.classList.remove('d-none');

        let pct = 0;
        const timer = setInterval(() => {
            pct += Math.floor(Math.random() * 12) + 6; // 6..17
            if (pct >= 95) pct = 95;
            progressBar.style.width = pct + '%';
            progressPct.textContent = pct + '%';
        }, 200);

        setTimeout(() => clearInterval(timer), 4000);
    });
});
</script>
@endsection
