<x-app-layout>
    @section('title', 'Edit Task')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-3">
                <a href="{{ route('mentor.tasks.index') }}" class="text-decoration-none text-muted small fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Tasks
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="card-title fw-bold mb-0">Edit Task: {{ $task->title }}</h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('mentor.tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Task Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Task Title</label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $task->title) }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="description" rows="4" class="form-control">{{ old('description', $task->description) }}</textarea>
                        </div>

                        <!-- Due Date -->
                        <div class="mb-4">
                            <label for="due_date" class="form-label fw-semibold">Due Date</label>
                            <input type="datetime-local" name="due_date" id="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $task->due_date->format('Y-m-d\TH:i')) }}" required>
                            @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Manage Documents -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Task Documents</label>
                            
                            @if($task->documents->count() > 0)
                                <div class="list-group mb-3">
                                    @foreach($task->documents as $doc)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center overflow-hidden">
                                                <div class="me-3 fs-5">{{ $doc->file_icon }}</div>
                                                <div class="text-truncate">
                                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="fw-medium text-decoration-none text-dark">{{ $doc->file_name }}</a>
                                                    <div class="text-muted small">{{ $doc->human_file_size }}</div>
                                                </div>
                                            </div>
                                            <div class="ms-2">
                                                <!-- Delete Document Button -->
                                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                                        onclick="if(confirm('Delete this file?')) document.getElementById('delete-doc-{{ $doc->id }}').submit()">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="p-3 bg-light rounded border border-dashed">
                                <label for="documents" class="form-label small fw-bold text-muted text-uppercase mb-2">Upload New Documents</label>
                                <input type="file" name="documents[]" id="documents" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip">
                                <div class="form-text">Allowed: PDF, Docs, Images, Zip. Max 10MB per file.</div>
                            </div>
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" name="is_active" id="is_active" value="1" {{ $task->is_active ? 'checked' : '' }}>
                            <label class="form-check-label fw-medium" for="is_active">Task is active (visible to participants)</label>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('mentor.tasks.index') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">Update Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@foreach($task->documents as $doc)
    <form id="delete-doc-{{ $doc->id }}" action="{{ route('mentor.documents.destroy', $doc) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
@endforeach
