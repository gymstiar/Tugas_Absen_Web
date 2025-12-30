<x-app-layout>
    @section('title', $task->title)

    <div class="mb-4">
        <a href="{{ route('mentor.tasks.index') }}" class="text-decoration-none text-muted small fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Back to Tasks
        </a>
    </div>

    <!-- Task Info Card -->
    <div class="card shadow border-0 overflow-hidden mb-4">
        <div class="card-header bg-primary bg-gradient text-white p-4 d-md-flex justify-content-between align-items-center border-bottom-0">
            <div>
                <h2 class="h3 fw-bold mb-1">{{ $task->title }}</h2>
                <p class="mb-0 opacity-75 fs-5">{{ $task->classGroup->name }}</p>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                 <form action="{{ route('mentor.tasks.toggle', $task) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-light text-primary fw-bold shadow-sm">
                        {{ $task->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
                 <form action="{{ route('mentor.tasks.toggleResubmission', $task) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-light fw-bold">
                        {{ $task->allow_resubmission ? 'Disable Resubmit' : 'Enable Resubmit' }}
                    </button>
                </form>
            </div>
        </div>
        
        <div class="card-body p-4">
            @if($task->description)
                <div class="p-3 bg-light rounded border mb-4">
                    {!! nl2br(e($task->description)) !!}
                </div>
            @endif

            <ul class="list-inline mb-4">
                 <li class="list-inline-item me-4 mb-2">
                    <i class="bi bi-calendar-event me-1 text-muted"></i> 
                    <span class="fw-bold">Due:</span> {{ $task->due_date->format('M d, Y H:i') }}
                    @if($task->isPastDue())
                         <span class="badge bg-danger ms-1">Overdue</span>
                    @else
                         <span class="badge bg-success ms-1">{{ $task->due_date->diffForHumans() }}</span>
                    @endif
                </li>
                <li class="list-inline-item me-4 mb-2">
                    <i class="bi bi-file-earmark me-1 text-muted"></i> <span class="fw-bold">Max Size:</span> {{ $task->getMaxFileSizeMB() }} MB
                </li>
                 <li class="list-inline-item me-4 mb-2">
                    <i class="bi bi-arrow-repeat me-1 text-muted"></i> <span class="fw-bold">Resubmission:</span> 
                    <span class="badge {{ $task->allow_resubmission ? 'bg-success' : 'bg-secondary' }}">
                        {{ $task->allow_resubmission ? 'Allowed' : 'Off' }}
                    </span>
                </li>
            </ul>

            <!-- Stats Grid -->
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="card bg-primary bg-opacity-10 border-0 h-100">
                         <div class="card-body text-center py-4">
                            <h2 class="display-6 fw-bold text-primary mb-0">{{ $stats['total'] }}</h2>
                            <small class="text-uppercase fw-bold text-primary opacity-75">Submitted</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                     <div class="card bg-success bg-opacity-10 border-0 h-100">
                         <div class="card-body text-center py-4">
                            <h2 class="display-6 fw-bold text-success mb-0">{{ $stats['graded'] }}</h2>
                            <small class="text-uppercase fw-bold text-success opacity-75">Graded</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                     <div class="card bg-warning bg-opacity-10 border-0 h-100">
                         <div class="card-body text-center py-4">
                            <h2 class="display-6 fw-bold text-warning mb-0">{{ $stats['pending'] }}</h2>
                            <small class="text-uppercase fw-bold text-warning opacity-75">Pending</small>
                        </div>
                    </div>
                </div>
                 <div class="col-6 col-md-3">
                     <div class="card bg-info bg-opacity-10 border-0 h-100">
                         <div class="card-body text-center py-4">
                             @if($gradingStats['average'])
                                <h2 class="display-6 fw-bold text-info mb-0">{{ number_format($gradingStats['average'], 1) }}</h2>
                            @else
                                <h2 class="display-6 fw-bold text-info mb-0">-</h2>
                            @endif
                            <small class="text-uppercase fw-bold text-info opacity-75">Avg Grade</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($task->documents->count() > 0)
        <div class="card shadow-sm border-0 mb-4">
             <div class="card-header bg-white py-3">
                <h6 class="card-title fw-bold mb-0 text-dark">
                    <i class="bi bi-paperclip me-2"></i> Task Reference Documents
                </h6>
            </div>
            <div class="list-group list-group-flush">
                @foreach($task->documents as $doc)
                    <div class="list-group-item d-flex justify-content-between align-items-center px-4">
                         <div class="d-flex align-items-center">
                            <i class="bi bi-file-earmark-text fs-4 text-primary me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $doc->file_name }}</h6>
                                <small class="text-muted">{{ strtoupper($doc->file_type) }}</small>
                            </div>
                        </div>
                        <div class="btn-group btn-group-sm">
                             <a href="{{ route('files.document', $doc) }}" target="_blank" class="btn btn-outline-primary">View</a>
                             <a href="{{ route('files.document', ['document' => $doc, 'action' => 'download']) }}" class="btn btn-outline-dark">Download</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- Submissions List -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                     <h5 class="card-title fw-bold mb-0 text-primary">
                        <i class="bi bi-inbox me-2"></i> Submissions
                    </h5>
                    <span class="badge bg-light text-dark border">{{ $submissions->count() }}</span>
                </div>
                <div class="list-group list-group-flush overflow-auto" style="max-height: 800px;">
                    @forelse($submissions as $submission)
                        <div class="list-group-item p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="mb-0 fw-bold">
                                        {{ $submission->participant->name }}
                                        @if($submission->participant->id_number)
                                            <span class="badge bg-light text-dark border ms-1 font-monospace">{{ $submission->participant->id_number }}</span>
                                        @endif
                                    </h6>
                                    <small class="text-muted d-block">
                                        Submitted: {{ $submission->submitted_at->format('M d, H:i') }}
                                        @if($submission->isLate())
                                            <span class="text-danger fw-bold ms-1">(Late)</span>
                                        @endif
                                    </small>
                                     @if($submission->gradedBy)
                                        <small class="text-success d-block mt-1">
                                            <i class="bi bi-check-circle-fill me-1"></i> Graded by {{ $submission->gradedBy->name }}
                                        </small>
                                    @endif
                                </div>
                                @if($submission->isGraded())
                                    <span class="badge bg-{{ $submission->getGradeColor() == 'green' ? 'success' : ($submission->getGradeColor() == 'yellow' ? 'warning' : 'danger') }} fs-6">
                                        {{ $submission->grade }} ({{ $submission->getGradeLetter() }})
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Not Graded</span>
                                @endif
                            </div>

                             @if($submission->comment)
                                <div class="bg-light p-3 rounded mb-3 border fst-italic small">
                                    "{{ $submission->comment }}"
                                </div>
                            @endif
                            
                             <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('files.submission', $submission) }}" target="_blank" class="btn btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> View File
                                    </a>
                                     <a href="{{ route('files.submission', ['submission' => $submission, 'action' => 'download']) }}" class="btn btn-outline-dark">
                                        <i class="bi bi-download me-1"></i> Download
                                    </a>
                                </div>
                            </div>

                             <!-- Grading Form -->
                            <form action="{{ route('mentor.submissions.grade', $submission) }}" method="POST" class="card bg-light border-0">
                                @csrf
                                <div class="card-body p-3">
                                    <h6 class="fw-bold small text-muted text-uppercase mb-2">Grade & Feedback</h6>
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <input type="number" name="grade" class="form-control form-control-sm" placeholder="0-100" min="0" max="100" step="0.01" value="{{ $submission->grade }}" required>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="feedback" class="form-control form-control-sm" placeholder="Enter feedback..." value="{{ $submission->feedback }}">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary btn-sm w-100">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @empty
                         <div class="text-center py-5 text-muted">
                            <p class="mb-0">No submissions to grade</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Pending List -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold mb-0 text-muted">
                        <i class="bi bi-clock me-2"></i> Pending
                    </h5>
                    <span class="badge bg-light text-dark border">{{ $notSubmitted->count() }}</span>
                </div>
                 <div class="list-group list-group-flush overflow-auto" style="max-height: 800px;">
                    @forelse($notSubmitted as $participant)
                        <div class="list-group-item d-flex align-items-center px-4 py-3">
                             <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-muted fw-bold me-3" style="width: 40px; height: 40px;">
                                {{ substr($participant->name, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">
                                    {{ $participant->name }}
                                    @if($participant->id_number)
                                        <span class="badge bg-light text-dark border ms-1 font-monospace small">{{ $participant->id_number }}</span>
                                    @endif
                                </h6>
                                <small class="text-muted">{{ $participant->email }}</small>
                            </div>
                        </div>
                    @empty
                         <div class="text-center py-5">
                            <div class="display-4 mb-2">🎉</div>
                            <p class="mb-0 fw-bold">All students have submitted!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
