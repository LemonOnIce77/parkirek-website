@extends('layouts.auth')

@section('content')
<div class="auth-bg">
    <div class="auth-topnav">
        <a href="{{ route('student.dashboard') }}" class="auth-toplink">Home</a>
        <a href="#" class="auth-toplink">Features</a>
    </div>

    <div class="auth-card">
        <div class="text-center mb-4">
            <h2 class="auth-title mb-1">Create Account</h2>
            <p class="auth-subtitle mb-0">Student Registration</p>
        </div>

        <form method="POST" action="{{ route('register.attempt') }}">
            @csrf

            <!-- Full Name -->
            <div class="mb-3">
                <label class="form-label auth-label">Full Name</label>
                <div class="auth-inputgroup">
                    <span class="auth-inputicon"><i class="bi bi-person"></i></span>
                    <input
                        type="text"
                        name="nama"
                        value="{{ old('nama') }}"
                        class="form-control auth-input auth-input--withicon"
                        placeholder="Full name"
                        required>
                </div>
                @error('nama') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label auth-label">Email Address</label>
                <div class="auth-inputgroup">
                    <span class="auth-inputicon"><i class="bi bi-envelope"></i></span>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="form-control auth-input auth-input--withicon"
                        placeholder="you@example.com"
                        required>
                </div>
                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label auth-label">Password</label>
                <div class="auth-inputgroup position-relative">
                    <span class="auth-inputicon"><i class="bi bi-lock"></i></span>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control auth-input auth-input--withicon pe-5"
                        placeholder="••••••••"
                        required>
                    <button
                        type="button"
                        class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2 text-muted"
                        onclick="togglePassword('password', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>

                <!-- Password rules -->
                <div class="form-text small">
                    Password must be at least <strong>8 characters</strong>.
                </div>

                @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label class="form-label auth-label">Confirm Password</label>
                <div class="auth-inputgroup position-relative">
                    <span class="auth-inputicon"><i class="bi bi-lock"></i></span>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="form-control auth-input auth-input--withicon pe-5"
                        placeholder="••••••••"
                        required>
                    <button
                        type="button"
                        class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2 text-muted"
                        onclick="togglePassword('password_confirmation', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Terms -->
            <div class="form-check mb-3">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="terms"
                    name="terms"
                    value="1"
                    {{ old('terms') ? 'checked' : '' }}
                    required>
                <label class="form-check-label auth-remember" for="terms">
                    I agree to the <a href="#" class="auth-link-strong">terms and conditions</a>
                </label>
                @error('terms') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn auth-btn-primary w-100 mb-3">
                Create Account
            </button>

            <div class="text-center">
                <small class="text-muted">
                    Already have an account?
                    <a href="{{ route('student.auth.login') }}" class="auth-link-strong">Sign In</a>
                </small>
            </div>
        </form>
    </div>
</div>

{{-- Toggle password --}}
<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endsection
