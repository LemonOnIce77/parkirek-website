@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-5 pt-3">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1 d-flex align-items-center gap-2">
                Timestamp Details
                <i class="bi bi-clock-history text-maroon"></i>
            </h1>
            <p class="text-muted fs-6 mb-0">Parking in & out activity history</p>
        </div>

        <a href="/dashboard" class="btn btn-outline-maroon">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <!-- Filter -->
    <div class="timestamp-filter mb-4">
        <div class="row g-3">
            <div class="col-lg-5">
                <input type="text" class="form-control filter-input"
                       placeholder="Search plate number...">
            </div>
            <div class="col-lg-4">
                <select class="form-select filter-input">
                    <option selected>All Vehicles</option>
                    <option>B 1234 XYZ — Toyota Avanza</option>
                    <option>B 5678 ABC — Honda CR-V</option>
                </select>
            </div>
            <div class="col-lg-3">
                <select class="form-select filter-input">
                    <option selected>Last 7 days</option>
                    <option>Today</option>
                    <option>Last 30 days</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="ts-card">

        <!-- TODAY -->
        <div class="ts-day">
            <div class="ts-day-title">
                Today <span class="text-maroon">27 December 2025</span>
            </div>

            <div class="ts-item">
                <div class="ts-time">13:03</div>
                <div class="ts-body">
                    <div class="ts-title">
                        Parking In
                        <span class="ts-badge badge-in">IN</span>
                    </div>
                    <div class="ts-meta">
                        <i class="bi bi-car-front me-1"></i>
                        B 1234 XYZ • Slot A-15
                    </div>
                </div>
                <div class="ts-dot dot-blue"></div>
            </div>

            <div class="ts-item">
                <div class="ts-time">16:34</div>
                <div class="ts-body">
                    <div class="ts-title">
                        Parking Out
                        <span class="ts-badge badge-out">OUT</span>
                    </div>
                    <div class="ts-meta">
                        <i class="bi bi-car-front me-1"></i>
                        B 1234 XYZ • Duration 3h 31m
                    </div>
                </div>
                <div class="ts-dot dot-green"></div>
            </div>
        </div>

        <!-- YESTERDAY -->
        <div class="ts-day">
            <div class="ts-day-title">
                Yesterday <span class="text-maroon">26 December 2025</span>
            </div>

            <div class="ts-item">
                <div class="ts-time">09:00</div>
                <div class="ts-body">
                    <div class="ts-title">
                        Parking In
                        <span class="ts-badge badge-in">IN</span>
                    </div>
                    <div class="ts-meta">
                        <i class="bi bi-car-front me-1"></i>
                        B 5678 ABC • Slot B-22
                    </div>
                </div>
                <div class="ts-dot dot-blue"></div>
            </div>

            <div class="ts-item">
                <div class="ts-time">11:00</div>
                <div class="ts-body">
                    <div class="ts-title">
                        Parking Out
                        <span class="ts-badge badge-out">OUT</span>
                    </div>
                    <div class="ts-meta">
                        <i class="bi bi-car-front me-1"></i>
                        B 5678 ABC • Duration 2h 00m
                    </div>
                </div>
                <div class="ts-dot dot-green"></div>
            </div>
        </div>

    </div>
</div>
@endsection
