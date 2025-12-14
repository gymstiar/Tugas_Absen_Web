<x-app-layout>
    @section('title', $task->title)

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-3">
                <a href="{{ route('participant.tasks.index') }}" class="text-decoration-none text-muted small fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Tasks
                </a>
            </div>

            <!-- Task Info Card -->
            <div class="card shadow-sm border-0 overflow-hidden mb-4">
                <div class="card-header bg-primary bg-gradient text-white py-3">
                    <h5 class="card-title fw-bold mb-0">{{ $task->title }}</h5>
                    <p class="mb-0 small text-white-50">{{ $task->classGroup->name }} | By {{ $task->mentor->name }}</p>
                </div>
                <div class="card-body p-4">
                    @if($task->description)
                        <div class="mb-4">
                            {!! nl2br(e($task->description)) !!}
                        </div>
                    @endif

                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-calendar-event me-1"></i> Due: {{ $task->due_date->format('M d, Y H:i') }}
                        </span>
                        
                        @if($task->isPastDue())
                             <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                Overdue
                            </span>
                        @else
                             <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                {{ $task->due_date->diffForHumans() }}
                            </span>
                        @endif

                        @if(!$task->is_active)
                             <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>

            @if($task->documents && $task->documents->count() > 0)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="card-title fw-bold mb-0 text-primary">
                            <i class="bi bi-paperclip me-2"></i> Task Documents
                        </h6>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($task->documents as $doc)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 fs-4 text-muted">
                                        {{ $doc->file_icon }} <!-- If file_icon returns raw emoji or icon class -->
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold small">{{ $doc->file_name }}</h6>
                                        <small class="text-muted" style="font-size: 0.75rem;">{{ $doc->human_file_size }} • {{ strtoupper($doc->file_type) }}</small>
                                    </div>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-outline-primary">View</a>
                                    <a href="{{ Storage::url($doc->file_path) }}" download class="btn btn-outline-dark">Download</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($submission)
                <!-- Submission Info -->
                <div class="card border-success shadow-none mb-4" style="background-color: #f0fff4;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="flex-shrink-0 bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-check-lg fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h5 class="fw-bold text-success mb-0">Task Submitted!</h5>
                                <div class="text-success small">
                                    {{ $submission->submitted_at->format('M d, Y H:i') }}
                                    @if($submission->isLate()) <span class="fw-bold text-warning ms-1">(Late)</span> @endif
                                </div>
                            </div>
                        </div>

                        @if($submission->comment)
                            <div class="bg-white rounded p-3 mb-3 border shadow-sm">
                                <small class="fw-bold text-muted d-block mb-1">Your Comment</small>
                                <p class="mb-0 small text-dark fst-italic">"{{ $submission->comment }}"</p>
                            </div>
                        @endif

                        <div class="d-flex gap-2">
                            <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="btn btn-success btn-sm">
                                <i class="bi bi-eye me-1"></i> View Submission
                            </a>
                            <a href="{{ route('participant.submissions.download', $submission) }}" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                </div>

                @if($submission->isGraded())
                    <!-- Grade Display -->
                    <div class="card shadow-sm border-0 mb-4 text-center overflow-hidden">
                        <div class="card-header bg-light py-3">
                            <h6 class="fw-bold text-dark mb-0">Your Grade</h6>
                        </div>
                        <div class="card-body p-5">
                            <h1 class="display-1 fw-bold text-{{ $submission->getGradeColor() == 'green' ? 'success' : ($submission->getGradeColor() == 'yellow' ? 'warning' : 'danger') }} mb-0">
                                {{ $submission->grade }}
                            </h1>
                            <small class="text-muted text-uppercase fw-bold ls-1">out of 100</small>
                            
                            @if($submission->feedback)
                                <div class="mt-4 p-3 bg-light rounded text-start mx-auto border" style="max-width: 500px;">
                                    <div class="small fw-bold text-muted mb-1">Mentor Feedback:</div>
                                    <p class="mb-0">{{ $submission->feedback }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="card bg-light border-0 text-center mb-4">
                        <div class="card-body py-5">
                            <div class="display-4 mb-3 text-info">⏳</div>
                            <h5 class="fw-bold text-muted">Awaiting Grading</h5>
                            <p class="text-muted small mb-0">Check back later for your grade and feedback.</p>
                        </div>
                    </div>
                @endif
            @endif

            @if($canSubmit)
                 <!-- Submit Form -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h5 class="card-title fw-bold mb-0">
                            {{ $submission ? '🔄 Resubmit Your Work' : '📤 Submit Your Work' }}
                        </h5>
                        <p class="text-muted small mb-0">
                            @if($submission)
                                <span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i> Warning: Resubmitting will replace your previous file.</span>
                            @else
                                Upload your completed task
                            @endif
                        </p>
                    </div>
                    
                    <div class="card-body p-4">
                        <form action="{{ route('participant.tasks.submit', $task) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-4 text-center p-4 bg-light rounded border border-dashed hover-bg-light transition">
                                <i class="bi bi-cloud-arrow-up display-4 text-primary mb-3 d-block"></i>
                                <label for="file" class="form-label fw-bold small text-uppercase text-muted">
                                    {{ $submission ? 'Upload New File' : 'Upload File' }}
                                </label>
                                <input type="file" name="file" id="file" class="form-control w-75 mx-auto" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip">
                                <div class="form-text mt-2">Max {{ $task->getMaxFileSizeMB() }}MB. Allowed: {{ implode(', ', $task->getAllowedFileTypesArray()) }}.</div>
                                @error('file')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-4">
                                <label for="comment" class="form-label fw-bold">Comment (Optional)</label>
                                <textarea name="comment" id="comment" rows="3" class="form-control" placeholder="Add any notes...">{{ old('comment') }}</textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary p-3 fw-bold shadow-sm">
                                    {{ $submission ? 'Resubmit Task' : 'Submit Task' }}
                                </button>
                                @if($task->isPastDue())
                                    <div class="text-center mt-2 small text-warning fw-bold">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Overdue - Late submission will be noted
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            @elseif(!$submission)
                 <div class="alert alert-light text-center border shadow-sm p-5">
                     <i class="bi bi-slash-circle fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="fw-bold text-muted">Task Closed</h5>
                    <p class="mb-0 text-muted small">This task is no longer accepting submissions.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
