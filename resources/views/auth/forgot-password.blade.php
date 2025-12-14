<x-guest-layout>
    <div class="text-center mb-4">
        <h4 class="fw-bold text-dark">Forgot Password?</h4>
        <p class="text-muted small">No problem. Enter your email and we'll send you a reset link.</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success small mb-3">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label fw-bold small text-muted">Email Address</label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm">Send Reset Link</button>
        </div>

        <div class="text-center">
            <a class="text-decoration-none small" href="{{ route('login') }}">
                <i class="bi bi-arrow-left me-1"></i> Back to Login
            </a>
        </div>
    </form>
</x-guest-layout>
