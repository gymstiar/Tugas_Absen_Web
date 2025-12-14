<x-app-layout>
    @section('title', 'Create Session')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title fw-bold mb-0">Create New Attendance Session</h5>
                        <a href="{{ route('mentor.attendance.index') }}" class="btn btn-light btn-sm rounded-pill">
                            <i class="bi bi-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('mentor.attendance.store') }}" method="POST">
                        @csrf
                        
                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-semibold">Session Title</label>
                            <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="e.g. Week 1: Introduction" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <!-- Open At -->
                            <div class="col-md-6">
                                <label for="open_at" class="form-label fw-semibold">Opens At</label>
                                <input type="datetime-local" class="form-control @error('open_at') is-invalid @enderror" id="open_at" name="open_at" value="{{ old('open_at') }}" required>
                                @error('open_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Close At -->
                            <div class="col-md-6">
                                <label for="close_at" class="form-label fw-semibold">Closes At</label>
                                <input type="datetime-local" class="form-control @error('close_at') is-invalid @enderror" id="close_at" name="close_at" value="{{ old('close_at') }}" required>
                                @error('close_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Optional description or notes...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end pt-3">
                            <a href="{{ route('mentor.attendance.index') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm">
                                Create Session
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
