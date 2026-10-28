@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-5 pt-3">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-6 col-xl-5">

            <div class="vehicle-form-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h4 class="fw-bold mb-0">Edit Vehicle</h4>
                        <small class="text-muted">Update your vehicle information</small>
                    </div>
                    <i class="bi bi-pencil-square text-maroon"></i>
                </div>

                {{-- Server-side errors --}}
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

                <form
                    id="vehicleEditForm"
                    method="POST"
                    action="{{ route('student.vehicles.update', $kendaraan->id) }}"
                    enctype="multipart/form-data"
                >
                    @csrf
                    @method('PUT')

                    <!-- License Plate -->
                    <div class="mb-3">
                        <label class="form-label small text-muted">License plate number</label>
                        <input
                            type="text"
                            name="plat_no"
                            value="{{ old('plat_no', $kendaraan->plat_no) }}"
                            class="form-control form-soft @error('plat_no') is-invalid @enderror"
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
                            <option value="mobil" {{ old('jenis_kendaraan', $kendaraan->jenis_kendaraan) === 'mobil' ? 'selected' : '' }}>
                                Car
                            </option>
                            <option value="motor" {{ old('jenis_kendaraan', $kendaraan->jenis_kendaraan) === 'motor' ? 'selected' : '' }}>
                                Motorcycle
                            </option>
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
                            value="{{ old('stnk_number', $kendaraan->stnk_number) }}"
                            class="form-control form-soft @error('stnk_number') is-invalid @enderror"
                        >
                        @error('stnk_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- STNK Photo (Drag & Drop + Preview) -->
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
                            <div class="text-muted small">or click to replace (JPG/PNG, max 5MB)</div>

                            <input
                                id="stnk_photo"
                                type="file"
                                name="stnk_photo"
                                accept="image/png,image/jpeg"
                                class="d-none"
                            >
                        </div>

                        <!-- Client error -->
                        <div id="stnkClientError" class="alert alert-danger py-2 px-3 mt-3 d-none mb-0">
                            <small id="stnkClientErrorText"></small>
                        </div>

                        <!-- Preview -->
                        <div id="stnkPreviewWrap" class="mt-3">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                <div class="small text-muted">
                                    <span id="stnkFileLabel">
                                        Current file:
                                        <span class="fw-semibold">
                                            {{ $kendaraan->stnk_photo_path ? basename($kendaraan->stnk_photo_path) : '-' }}
                                        </span>
                                    </span>
                                </div>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger d-none"
                                    id="stnkRemoveBtn"
                                >
                                    <i class="bi bi-x-circle me-1"></i> Remove
                                </button>
                            </div>

                            <div class="ratio ratio-16x9 rounded-4 overflow-hidden border bg-white">
                                @if ($kendaraan->stnk_photo_path)
                                    <img
                                        id="stnkPreviewImg"
                                        src="{{ asset('storage/'.$kendaraan->stnk_photo_path) }}"
                                        class="w-100 h-100"
                                        style="object-fit:cover;"
                                    >
                                @else
                                    <img id="stnkPreviewImg" src="" class="d-none">
                                @endif
                            </div>
                        </div>

                        @error('stnk_photo')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Note -->
                    <div class="alert alert-warning small mb-3">
                        <strong>Note:</strong> Updating STNK photo may require re-verification by admin.
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-maroon w-100">
                            <i class="bi bi-save me-1"></i> Save Changes
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
    const imgEl = document.getElementById('stnkPreviewImg');
    const removeBtn = document.getElementById('stnkRemoveBtn');

    const MAX_BYTES = 5 * 1024 * 1024;
    const ALLOWED = ['image/jpeg', 'image/png'];

    function showError(msg){
        errText.textContent = msg;
        errBox.classList.remove('d-none');
    }

    function clearError(){
        errBox.classList.add('d-none');
        errText.textContent = '';
    }

    function setFile(file){
        clearError();

        if (!ALLOWED.includes(file.type)) {
            showError('File must be JPG or PNG');
            return;
        }

        if (file.size > MAX_BYTES) {
            showError('File too large. Max 5MB');
            return;
        }

        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;

        const url = URL.createObjectURL(file);
        imgEl.src = url;
        imgEl.classList.remove('d-none');
        removeBtn.classList.remove('d-none');

        imgEl.onload = () => URL.revokeObjectURL(url);
    }

    dropzone.addEventListener('click', () => input.click());

    input.addEventListener('change', () => {
        if (input.files[0]) setFile(input.files[0]);
    });

    dropzone.addEventListener('dragover', e => e.preventDefault());
    dropzone.addEventListener('drop', e => {
        e.preventDefault();
        if (e.dataTransfer.files[0]) setFile(e.dataTransfer.files[0]);
    });

    removeBtn.addEventListener('click', () => {
        input.value = '';
        imgEl.src = '';
        imgEl.classList.add('d-none');
        removeBtn.classList.add('d-none');
    });
});
</script>
@endsection
