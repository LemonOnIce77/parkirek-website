@extends('layouts.auth')

@section('content')
<div class="auth-bg">

    <div class="auth-topnav">
        <a href="/" class="auth-toplink">Home</a>
    </div>

    <div class="auth-card">
        <div class="text-center mb-4">
            <h2 class="auth-title mb-1 text-dark">ParkiRek</h2>
            <p class="auth-subtitle mb-0">Security & Staff Portal</p>
        </div>

        {{-- flash error message --}}
        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        {{-- validation errors --}}
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.attempt') }}" method="POST">
            @csrf

            {{-- penanda login admin --}}
            <input type="hidden" name="login_as" value="admin">

            <div class="mb-3">
                <label class="form-label auth-label">
                    <i class="bi bi-shield-lock me-2"></i>Staff Email / ID
                </label>
                <input
                    type="email"
                    name="email"
                    class="form-control auth-input"
                    placeholder="admin@parkirek.com"
                    value="{{ old('email') }}"
                    required
                >
            </div>

            <div class="mb-2">
                <label class="form-label auth-label">
                    <i class="bi bi-key me-2"></i>Password
                </label>

                {{-- Password with toggle --}}
                <div class="position-relative">
                    <input
                        type="password"
                        name="password"
                        id="admin_password"
                        class="form-control auth-input pe-5"
                        placeholder="••••••••"
                        required
                    >

                    <button
                        type="button"
                        class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2 text-muted"
                        onclick="togglePassword('admin_password', this)"
                        aria-label="Toggle password visibility"
                        style="border:0; background:transparent; padding:6px;"
                    >
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember_admin" name="remember" value="1">
                    <label class="form-check-label auth-remember" for="remember_admin">
                        Remember me
                    </label>
                </div>
            </div>

            <button
                type="submit"
                class="btn auth-btn-primary w-100 mb-3"
                style="background-color: #9F1421; border-color: #9F1421;"
            >
                Login as Admin
            </button>

            <div class="auth-footer text-center mt-4">
                <small class="text-muted">
                    Not an admin?
                    <a href="{{ route('student.auth.login') }}" class="auth-security" style="color: #9F1421;">
                        Student Login Here
                    </a>
                </small>
            </div>
        </form>
    </div>
</div>

{{-- Toggle password script --}}
<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');

        if (!input) return;

        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';

        icon.classList.toggle('bi-eye', !isHidden);
        icon.classList.toggle('bi-eye-slash', isHidden);
    }
</script>
@endsection
