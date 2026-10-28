@extends('layouts.app')

@section('content')

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-maroon d-flex align-items-center gap-2" href="{{ url('/') }}">
            <span class="logo-circle">P</span>
            <span class="fs-4 tracking-tight">ParkiRek</span>
        </a>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('student.auth.login') }}" class="btn btn-outline-maroon btn-sm px-3 d-none d-sm-inline-flex">
                <i class="bi bi-person"></i> Student
            </a>
            <a href="{{ route('admin.auth.login') }}" class="btn btn-maroon btn-sm px-3">
                <i class="bi bi-shield-lock"></i> Security Portal
            </a>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero-section text-center">
    <div class="container position-relative" style="z-index: 1;">
        <!-- Dynamic Announcement Pill -->
        <div class="hero-pill">
            <span class="pulse-dot"></span>
            <span>Telkom University Surabaya • Smart Campus 2026</span>
        </div>

        <h1 class="hero-title">
            Smart Digital Parking for <br>
            <span>Telkom University</span>
        </h1>

        <p class="hero-subtitle">
            Digital vehicle check-in, real-time violation monitoring, and instant QR verification.
            Say goodbye to paper tickets and lost parking stubs.
        </p>

        <div class="d-flex justify-content-center flex-wrap gap-3 mt-4">
            <a href="{{ route('student.auth.login') }}" class="btn btn-maroon px-4 py-2">
                <i class="bi bi-arrow-right-circle"></i> Student Access
            </a>
            <a href="{{ route('admin.auth.login') }}" class="btn btn-outline-maroon px-4 py-2">
                <i class="bi bi-shield-check"></i> Security Portal
            </a>
        </div>

        <!-- Trust Badges -->
        <div class="hero-badges">
            <div class="hero-badge-item">
                <i class="bi bi-shield-fill-check"></i> 100% Verified Vehicles
            </div>
            <div class="hero-badge-item">
                <i class="bi bi-qr-code-scan"></i> Real-time QR Scanning
            </div>
            <div class="hero-badge-item">
                <i class="bi bi-speedometer2"></i> Zero Gate Queues
            </div>
        </div>
    </div>
</section>

<!-- QUICK FEATURES -->
<section class="features-section">
    <div class="container">
        <div class="row g-4 justify-content-center">

            <div class="col-md-5">
                <div class="feature-card text-center">
                    <div class="parking-icon">
                        <span>P</span>
                    </div>
                    <h4 class="fw-bold text-dark">Quick Registration</h4>
                    <p class="text-muted">Register vehicles with STNK verification in seconds</p>
                </div>
            </div>

            <div class="col-md-5">
                <div class="feature-card text-center">
                    <div class="qr-icon">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Digital QR Barcode</h4>
                    <p class="text-muted">Unique encrypted gate pass generated for each vehicle</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- FULL FEATURES SECTION -->
<section id="features" class="features-full-section">
    <div class="container">

        <div class="text-center mb-5">
            <span class="text-uppercase fw-bold text-maroon small tracking-wider" style="letter-spacing: .1em;">Engineered For Campus Safety</span>
            <h2 class="fw-bold mt-2" style="font-size: 2.2rem;">Powerful Campus Features</h2>
            <p class="text-muted" style="max-width: 600px; margin: 0 auto;">
                Everything students and campus security need for a frictionless, orderly parking experience.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="bi bi-car-front-fill"></i>
                    </div>
                    <h5>Vehicle Management</h5>
                    <p>Register multiple motorcycles or cars with official plate numbers and verified STNK documents.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="bi bi-qr-code"></i>
                    </div>
                    <h5>Digital Gate Passes</h5>
                    <p>Instant contactless QR code for seamless scanning at Telkom University campus gates.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <h5>Instant Scan & Go</h5>
                    <p>Security officers scan student QR passes in less than 2 seconds, eliminating entrance queues.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h5>Violation Reporting</h5>
                    <p>Report chaotic or disorderly parking with instant photo evidence upload and follow-up tracking.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="bi bi-bar-chart-line-fill"></i>
                    </div>
                    <h5>Real-time Analytics</h5>
                    <p>Live security dashboard with capacity tracking, entry/exit trends, and violation metrics.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="bi bi-lock-fill"></i>
                    </div>
                    <h5>Encrypted & Secure</h5>
                    <p>Role-based access control safeguarding student personal records, license numbers, and STNKs.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CHOOSE ROLE SECTION -->
<div class="container choose-role-section" id="for-you">
    <div class="text-center mb-5">
        <span class="text-uppercase fw-bold text-maroon small" style="letter-spacing: .1em;">Choose Portal</span>
        <h2 class="fw-bold mt-2">Get Started with ParkiRek</h2>
        <p class="text-muted">Select your portal to log in or register</p>
    </div>

    <div class="row justify-content-center g-4">
        <!-- STUDENT -->
        <div class="col-md-5">
            <div class="role-card">
                <span class="role-badge">
                    <i class="bi bi-mortarboard-fill"></i> Student Portal
                </span>

                <h3 class="fw-bold mb-3">For Students</h3>

                <ul class="role-list">
                    <li><i class="bi bi-check-circle-fill"></i> Register and manage your vehicles anytime</li>
                    <li><i class="bi bi-check-circle-fill"></i> Upload STNK and vehicle photos for verification</li>
                    <li><i class="bi bi-check-circle-fill"></i> Instant QR barcode for hassle-free gate entry</li>
                    <li><i class="bi bi-check-circle-fill"></i> View real-time entry and exit timestamps</li>
                    <li><i class="bi bi-check-circle-fill"></i> Report parking violations with photo evidence</li>
                    <li><i class="bi bi-check-circle-fill"></i> Track personal violation points and resolution</li>
                </ul>

                <a href="{{ route('student.auth.login') }}" class="btn-student">
                    <i class="bi bi-person-check me-1"></i> Student Login / Register
                </a>
            </div>
        </div>

        <!-- SECURITY -->
        <div class="col-md-5">
            <div class="role-card">
                <span class="role-badge">
                    <i class="bi bi-shield-fill"></i> Security Staff
                </span>

                <h3 class="fw-bold mb-3">For Security Officers</h3>

                <ul class="role-list">
                    <li><i class="bi bi-check-circle-fill"></i> High-speed camera QR code scanner</li>
                    <li><i class="bi bi-check-circle-fill"></i> Instant vehicle & student data verification</li>
                    <li><i class="bi bi-check-circle-fill"></i> Real-time tracking of active parked vehicles</li>
                    <li><i class="bi bi-check-circle-fill"></i> Manage and update parking violation reports</li>
                    <li><i class="bi bi-check-circle-fill"></i> Access full campus parking logs and history</li>
                    <li><i class="bi bi-check-circle-fill"></i> Live daily analytics and security statistics</li>
                </ul>

                <a href="{{ route('admin.auth.login') }}" class="btn-security">
                    <i class="bi bi-shield-lock me-1"></i> Security Staff Portal
                </a>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="landing-footer">
    <div class="container">
        <div class="row gy-4 align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <div class="footer-brand d-inline-flex align-items-center gap-2 mb-2">
                    <span class="logo-circle" style="width: 28px; height: 28px; font-size: 0.9rem;">P</span>
                    <span>ParkiRek</span>
                </div>
                <p class="footer-tagline mb-0">
                    Sistem Manajemen Parkir Digital Terintegrasi Telkom University Surabaya.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0 small text-muted">
                    &copy; {{ date('Y') }} ParkiRek • Telkom University Surabaya. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</footer>

@endsection
