<x-app-layout>
    @section('title', 'Edit User')

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="d-grid mb-3">
                <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted small fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Users
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="card-title fw-bold mb-0">Edit User: {{ $user->name }}</h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ID Number -->
                        <div class="mb-3">
                            <label for="id_number" class="form-label fw-semibold">ID Number <span class="text-muted fw-normal small">(Student/Employee ID)</span></label>
                            <input type="text" class="form-control" id="id_number" name="id_number" value="{{ old('id_number', $user->id_number) }}" placeholder="e.g., 2024001234">
                            @error('id_number')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="mb-3">
                            <label for="role" class="form-label fw-semibold">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="participant" {{ old('role', $user->role) == 'participant' ? 'selected' : '' }}>Participant</option>
                                <option value="mentor" {{ old('role', $user->role) == 'mentor' ? 'selected' : '' }}>Mentor</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <h6 class="text-muted fw-bold small text-uppercase mb-3">Change Password</h6>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">New Password <span class="text-muted fw-normal small">(leave blank to keep current)</span></label>
                            <input type="password" class="form-control" id="password" name="password">
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirmation -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
