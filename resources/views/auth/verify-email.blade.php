<x-guest-layout>
    <div class="text-center mb-4">
        <div class="mb-3">
            <i class="bi bi-envelope-check display-4 text-primary"></i>
        </div>
        <h4 class="fw-bold text-dark">Verify Your Email</h4>
        <p class="text-muted small">Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success small mb-3">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary fw-bold shadow-sm">Resend Verification Email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-muted small">Log Out</button>
        </form>
    </div>
</x-guest-layout>
