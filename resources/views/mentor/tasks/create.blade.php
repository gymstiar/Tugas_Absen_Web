<x-app-layout>
    @section('title', 'Create Task')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-3">
                <a href="{{ route('mentor.tasks.index') }}" class="text-decoration-none text-muted small fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Tasks
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary bg-gradient text-white py-3">
                    <h5 class="card-title fw-bold mb-0">Create New Task</h5>
                    <p class="mb-0 small text-white-50">Add a new task for your class with submission settings</p>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('mentor.tasks.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <strong><i class="bi bi-exclamation-triangle me-2"></i>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <!-- Class Selection -->
                        <div class="mb-3">
                            <label for="class_group_id" class="form-label fw-semibold">Select Class <span class="text-danger">*</span></label>
                            <select name="class_group_id" id="class_group_id" class="form-select @error('class_group_id') is-invalid @enderror" required>
                                <option value="">Choose a class...</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_group_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }} ({{ $class->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('class_group_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Task Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Task Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g., Week 1 Assignment" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="description" rows="4" class="form-control" placeholder="Describe the task requirements...">{{ old('description') }}</textarea>
                        </div>

                        <!-- Due Date -->
                        <div class="mb-4">
                            <label for="due_date" class="form-label fw-semibold">Due Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="due_date" id="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}" required>
                            @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Submission Settings -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-gear-fill me-2"></i>Submission Settings</h6>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" name="allow_resubmission" id="allow_resubmission" value="1" {{ old('allow_resubmission') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_resubmission">Allow participants to resubmit (replaces previous submission)</label>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="max_file_size" class="form-label small fw-bold">Max File Size (KB)</label>
                                        <input type="number" name="max_file_size" id="max_file_size" class="form-control" value="{{ old('max_file_size', 10240) }}" min="1">
                                        <div class="form-text">Default: 10MB (10240 KB).</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="allowed_file_types" class="form-label small fw-bold">Allowed File Types</label>
                                        <input type="text" name="allowed_file_types" id="allowed_file_types" class="form-control" value="{{ old('allowed_file_types', 'pdf,docx,doc,zip,jpg,png') }}" placeholder="pdf,docx,zip...">
                                        <div class="form-text">Comma-separated extentions</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Document Upload -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Upload Task Documents (Optional)</label>
                            <input type="file" name="documents[]" class="form-control" multiple>
                            <div class="form-text">Upload reference materials (PDF, DOC, ZIP).</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('mentor.tasks.index') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">Create Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
