<section>
    <header>
        <h5 class="fw-bold text-dark">Profile Information</h5>
        <p class="text-muted small">Update your account's profile information and email address.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-4">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label fw-bold small text-uppercase text-muted">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-bold small text-uppercase text-muted">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="id_number" class="form-label fw-bold small text-uppercase text-muted">ID Number <span class="text-muted fw-normal">(Student/Employee ID)</span></label>
            <input type="text" name="id_number" id="id_number" class="form-control" value="{{ old('id_number', $user->id_number) }}" placeholder="e.g., 2024001234">
            @error('id_number')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            <small class="text-muted">Your unique identification number for clearer identification.</small>
        </div>

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-gray-800">
                        Your email address is unverified.
                        <button form="send-verification" class="btn btn-link p-0 align-baseline text-sm">
                            Click here to re-send the verification email.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 fw-medium text-success small">
                            A new verification link has been sent to your email address.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Save Changes</button>

            @if (session('status') === 'profile-updated')
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
