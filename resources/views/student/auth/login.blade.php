@extends('layouts.auth')

@section('content')
    <div class="auth-bg">
        <div class="auth-topnav">
            <a href="{{ url('/') }}" class="auth-toplink">Home</a>
        </div>

        <div class="auth-card">
            <div class="text-center mb-4">
                <h2 class="auth-title mb-1">ParkirRek</h2>
                <p class="auth-subtitle mb-0">Student Login</p>
            </div>

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf

                <input type="hidden" name="login_as" value="student">

                <div class="mb-3">
                    <label class="form-label auth-label">
                        <i class="bi bi-envelope me-2"></i>Email Address
                    </label>
                    <input
                        type="email"
                        name="email"
                        class="form-control auth-input"
                        placeholder="you@example.com"
                        value="{{ old('email') }}"
                        required
                        autofocus
                    >
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-2">
                    <label class="form-label auth-label">
                        <i class="bi bi-lock me-2"></i>Password
                    </label>

                    {{-- Password with toggle --}}
                    <div class="position-relative">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control auth-input pe-5"
                            placeholder="••••••••"
                            required
                        >

                        <button
                            type="button"
                            class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2 text-muted"
                            onclick="togglePassword('password', this)"
                            aria-label="Toggle password visibility"
                            style="border:0; background:transparent; padding: 6px;"
                        >
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
                        <label class="form-check-label auth-remember" for="remember">
                            Remember me
                        </label>
                    </div>

                </div>

                <button class="btn auth-btn-primary w-100 mb-3" type="submit">
                    Sign In
                </button>

                <div class="auth-divider my-3">
                    <span>Don’t have an account?</span>
                </div>

                <a href="{{ route('student.auth.register') }}" class="btn auth-btn-outline w-100">
                    Create Account
                </a>
            </form>

            <div class="auth-footer text-center mt-4">
                <small class="text-muted">
                    Security?
                    <a href="{{ route('admin.auth.login') }}" class="auth-security">
                        Security Login
                    </a>
                </small>
            </div>
        </div>
    </div>

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
