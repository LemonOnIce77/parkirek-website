@extends('layouts.auth')

@section('content')
<div class="auth-bg">
    
    <div class="auth-topnav">
        <a href="/" class="auth-toplink">Home</a>
    </div>


    <div class="auth-card">
        <div class="text-center mb-4">

            <h2 class="auth-title mb-1 text-dark">ParkiRek</h2>
            <p class="auth-subtitle mb-0">Admin Registration</p>
        </div>


        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert" style="font-size: 0.85rem;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>
                <strong>Demo Token:</strong> <code>PARKIREK2023</code>
            </div>
        </div>

        <form action="{{ route('admin.login') }}">

            <div class="mb-3">
                <label class="form-label auth-label">
                    <i class="bi bi-person me-2"></i>Full Name
                </label>
                <input type="text" class="form-control auth-input" placeholder="Officer Name">
            </div>


            <div class="mb-3">
                <label class="form-label auth-label">
                    <i class="bi bi-envelope me-2"></i>Email Address
                </label>
                <input type="email" class="form-control auth-input" placeholder="admin@parkirek.com">
            </div>


            <div class="mb-3">
                <label class="form-label auth-label text-danger">
                    <i class="bi bi-shield-exclamation me-2"></i>Security Token
                </label>
                <input type="text" class="form-control auth-input" placeholder="Enter Campus Token" value="PARKIREK2023">
            </div>


            <div class="mb-3">
                <label class="form-label auth-label">
                    <i class="bi bi-lock me-2"></i>Password
                </label>
                <input type="password" class="form-control auth-input" placeholder="Create a password">
            </div>


            <div class="mb-4">
                <label class="form-label auth-label">
                    <i class="bi bi-check-circle me-2"></i>Confirm Password
                </label>
                <input type="password" class="form-control auth-input" placeholder="Repeat password">
            </div>


            <button type="submit" class="btn auth-btn-primary w-100 mb-3" style="background-color: #9F1421; border-color: #9F1421;">
                Register Access
            </button>

            <div class="auth-divider my-3">
                <span>Already have access?</span>
            </div>


            <a href="{{ route('admin.login') }}" class="btn auth-btn-outline w-100">
                Back to Admin Login
            </a>
        </form>
    </div>
</div>
@endsection
