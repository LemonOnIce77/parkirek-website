@extends('layouts.auth')

@section('content')
<div class="auth-bg">
    <!-- Top Right Nav -->
    <div class="auth-topnav">
        <a href="/" class="auth-toplink">Home</a>
        <a href="#" class="auth-toplink">Features</a>
    </div>

    <!-- Card -->
    <div class="auth-card">
        <div class="text-center mb-4">
            <h2 class="auth-title mb-1">ParkirRek</h2>
            <p class="auth-subtitle mb-0">Password reset</p>
        </div>

        {{-- Next akan redirect ke halaman OTP --}}
        <form action="{{ url('/verify-code') }}" method="GET">
            <!-- Email -->
            <div class="mb-3">
                <label class="form-label auth-label">
                    <i class="bi bi-envelope me-2"></i>Enter your email
                </label>
                <input type="email" class="form-control auth-input" placeholder="you@example.com" required>
            </div>

            <div class="d-flex justify-content-end mb-3">
                <a href="#" class="auth-forgot">Forgot Email?</a>
            </div>

            <!-- Next -->
            <button type="submit" class="btn auth-btn-primary w-100 mb-3">
                Next
            </button>

            <div class="auth-divider my-3">
                <span>Already have an account?</span>
            </div>

            <!-- Sign In -->
            <a href="/login" class="btn auth-btn-outline w-100">
                Sign In
            </a>

            <div class="auth-footer text-center mt-4">
                <small class="text-muted">Security? <span class="auth-security">Security Login</span></small>
            </div>
        </form>
    </div>
</div>
@endsection
