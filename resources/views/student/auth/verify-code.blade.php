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
        <div class="text-center mb-3">
            <h2 class="auth-title mb-1">ParkirRek</h2>
            <p class="auth-subtitle mb-0">Password reset</p>
        </div>

        <div class="text-center auth-hint mb-3">
            <div class="d-inline-flex align-items-center gap-2 text-muted">
                <i class="bi bi-envelope"></i>
                <span>We sent some code for you to make sure it’s you</span>
            </div>
        </div>

        <!-- OTP Inputs -->
        <div class="otp-wrap mb-2">
            <input class="otp-input" type="text" inputmode="numeric" maxlength="1" />
            <input class="otp-input" type="text" inputmode="numeric" maxlength="1" />
            <input class="otp-input" type="text" inputmode="numeric" maxlength="1" />
            <input class="otp-input" type="text" inputmode="numeric" maxlength="1" />
            <input class="otp-input" type="text" inputmode="numeric" maxlength="1" />
            <input class="otp-input" type="text" inputmode="numeric" maxlength="1" />
        </div>

        <div class="d-flex justify-content-end mb-3">
            <a href="#" class="auth-forgot">Re-send code</a>
        </div>

        <button type="button" class="btn auth-btn-primary w-100 mb-3">
            Next
        </button>

        <div class="auth-divider my-3">
            <span>Already have an account?</span>
        </div>

        <a href="/login" class="btn auth-btn-outline w-100">
            Sign In
        </a>

        <div class="auth-footer text-center mt-4">
            <small class="text-muted">Security? <span class="auth-security">Security Login</span></small>
        </div>
    </div>
</div>

<!-- Auto move to next box (optional nice UX) -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const inputs = Array.from(document.querySelectorAll('.otp-input'));
  inputs.forEach((input, idx) => {
    input.addEventListener('input', (e) => {
      e.target.value = e.target.value.replace(/[^0-9]/g, '').slice(0, 1);
      if (e.target.value && inputs[idx + 1]) inputs[idx + 1].focus();
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !input.value && inputs[idx - 1]) {
        inputs[idx - 1].focus();
      }
    });
  });
});
</script>
@endsection
