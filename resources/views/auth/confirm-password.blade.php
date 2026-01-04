<x-guest-layout>
    <div class="text-center mb-4">
        <h4 class="fw-bold text-dark">Confirm Password</h4>
        <p class="text-muted small">This is a secure area. Please confirm your password before continuing.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="form-label fw-bold small text-muted">Password</label>
            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm">Confirm</button>
        </div>
    </form>
</x-guest-layout>
