<x-guest-layout>
    <div class="text-center mb-4">
        <h4 class="fw-bold text-dark">Create Account</h4>
        <p class="text-muted small">Fill in your details to register</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label fw-bold small text-muted">Name</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label fw-bold small text-muted">Email</label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autocomplete="username">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label fw-bold small text-muted">Password</label>
            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-bold small text-muted">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required autocomplete="new-password">
            @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm">Register</button>
        </div>

        <div class="text-center">
            <a class="text-decoration-none small" href="{{ route('login') }}">
                Already registered? <span class="fw-bold">Sign in</span>
            </a>
        </div>
    </form>
</x-guest-layout>
