<section>
    <header>
        <h5 class="fw-bold text-dark">Update Password</h5>
        <p class="text-muted small">Ensure your account is using a long, random password to stay secure.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="current_password" class="form-label fw-bold small text-uppercase text-muted">Current Password</label>
            <input type="password" name="current_password" id="current_password" class="form-control" autocomplete="current-password">
            @error('current_password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-bold small text-uppercase text-muted">New Password</label>
            <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
            @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label fw-bold small text-uppercase text-muted">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password">
            @error('password_confirmation')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Save Password</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-success fw-bold mb-0"
                ><i class="bi bi-check-circle me-1"></i> Saved.</p>
            @endif
        </div>
    </form>
</section>
