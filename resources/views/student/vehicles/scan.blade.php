@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h3 class="fw-bold">Vehicle Detail</h3>

  <div class="card mt-3">
    <div class="card-body">
      <div><span class="text-muted">Plate:</span> <b>{{ $kendaraan->plat_no }}</b></div>
      <div class="mt-2"><span class="text-muted">Type:</span> <b>{{ strtoupper($kendaraan->jenis_kendaraan) }}</b></div>
      <div class="mt-2"><span class="text-muted">STNK Number:</span> <b>{{ $kendaraan->stnk_number }}</b></div>
    </div>
  </div>
</div>
@endsection
