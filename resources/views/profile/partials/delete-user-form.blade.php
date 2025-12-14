<section class="space-y-6">
    <header>
        <h5 class="fw-bold text-danger">Delete Account</h5>
        <p class="text-muted small">Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.</p>
    </header>

    <button type="button" class="btn btn-danger fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        Delete Account
    </button>

    <!-- Modal -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="{{ route('profile.destroy') }}" class="modal-content">
                @csrf
                @method('delete')

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="confirmUserDeletionModalLabel">Are you sure you want to delete your account?</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <p class="text-muted mb-4">
                        Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
                    </p>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold sr-only">Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                        @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold">Delete Account</button>
                </div>
            </form>
        </div>
    </div>
</section>
