<x-app-layout>
    @section('title', 'Edit Class Group')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title fw-bold mb-0">Edit Class Group</h5>
                        <a href="{{ route('admin.classes.index') }}" class="btn btn-light btn-sm rounded-pill">
                            <i class="bi bi-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.classes.update', $class) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold">Class Name</label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $class->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Code -->
                        <div class="mb-4">
                            <label for="code" class="form-label fw-semibold">Class Code</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-qr-code"></i></span>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $class->code) }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                         <!-- Mentor Assignment -->
                        <div class="mb-4">
                            <label for="mentor_id" class="form-label fw-semibold">Assign Mentor</label>
                            <select class="form-select @error('mentor_id') is-invalid @enderror" id="mentor_id" name="mentor_id" required>
                                <option value="" disabled>Select a mentor...</option>
                                
                                {{-- Current Mentor (if exists) --}}
                                @if($class->mentor)
                                    <option value="{{ $class->mentor_id }}" selected>
                                        {{ $class->mentor->name }} ({{ $class->mentor->email }})
                                    </option>
                                @endif

                                {{-- Available Mentors --}}
                                @foreach($availableMentors as $mentor)
                                    <option value="{{ $mentor->id }}" {{ old('mentor_id') == $mentor->id ? 'selected' : '' }}>
                                        {{ $mentor->name }} ({{ $mentor->email }})
                                    </option>
                                @endforeach
                            </select>
                             @error('mentor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">Change mentor if needed. Only unassigned mentors are shown.</div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $class->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end pt-3">
                            <a href="{{ route('admin.classes.index') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm">
                                Update Class
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
