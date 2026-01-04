<x-app-layout>
    @section('title', $attendance->title)

    <div class="mb-4">
        <a href="{{ route('mentor.attendance.index') }}" class="text-decoration-none text-muted small fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Back to Sessions
        </a>
    </div>

    <!-- Session Info Card -->
    <div class="card shadow border-0 overflow-hidden mb-4">
        <div class="card-header bg-primary bg-gradient text-white p-4 d-md-flex justify-content-between align-items-center border-bottom-0">
            <div>
                <h2 class="h3 fw-bold mb-1">{{ $attendance->title }}</h2>
                <p class="mb-0 opacity-75 fs-5">{{ $attendance->classGroup->name }}</p>
            </div>
            <form action="{{ route('mentor.attendance.toggle', $attendance) }}" method="POST" class="mt-3 mt-md-0">
                @csrf
                <button type="submit" class="btn {{ $attendance->is_open ? 'btn-danger' : 'btn-light text-primary' }} fw-bold shadow-sm">
                    @if($attendance->is_open)
                        <i class="bi bi-stop-circle me-2"></i> Close Session
                    @else
                        <i class="bi bi-play-circle me-2"></i> Re-open Session
                    @endif
                </button>
            </form>
        </div>
        
        <div class="card-body p-4">
            @if($attendance->description)
                <p class="lead text-muted mb-4">{{ $attendance->description }}</p>
            @endif

            <div class="d-flex flex-wrap gap-2 mb-4">
                <span class="badge bg-light text-dark border p-2 fw-normal d-flex align-items-center">
                    <i class="bi bi-calendar-event me-2 text-muted"></i>
                    {{ $attendance->open_at->format('M d, Y H:i') }} - {{ $attendance->close_at->format('H:i') }}
                </span>
                <span class="badge {{ $attendance->is_open ? 'bg-success bg-opacity-10 text-success border-success' : 'bg-secondary bg-opacity-10 text-secondary border-secondary' }} border border-opacity-25 p-2 fw-bold d-flex align-items-center">
                    @if($attendance->is_open)
                        <span class="spinner-grow spinner-grow-sm me-2" style="width: 0.5rem; height: 0.5rem;"></span>
                        Active Session
                    @else
                        <i class="bi bi-check-circle me-2"></i> Session Closed
                    @endif
                </span>
            </div>

            <!-- Stats Grid -->
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="card bg-success bg-opacity-10 border-0 h-100">
                        <div class="card-body text-center py-4">
                            <h2 class="display-6 fw-bold text-success mb-0">{{ $statusCounts['present'] }}</h2>
                            <small class="text-uppercase fw-bold text-success opacity-75">Present</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card bg-warning bg-opacity-10 border-0 h-100">
                        <div class="card-body text-center py-4">
                            <h2 class="display-6 fw-bold text-warning mb-0">{{ $statusCounts['permission'] }}</h2>
                            <small class="text-uppercase fw-bold text-warning opacity-75">Permission</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card bg-danger bg-opacity-10 border-0 h-100">
                        <div class="card-body text-center py-4">
                            <h2 class="display-6 fw-bold text-danger mb-0">{{ $statusCounts['sick'] }}</h2>
                            <small class="text-uppercase fw-bold text-danger opacity-75">Sick</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card bg-primary bg-opacity-10 border-0 h-100">
                        <div class="card-body text-center py-4">
                            <h2 class="display-6 fw-bold text-primary mb-0">{{ $statusCounts['total'] }}</h2>
                            <small class="text-uppercase fw-bold text-primary opacity-75">Total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Submitted Attendances -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold mb-0 text-success">
                        <i class="bi bi-check-all me-2"></i> Submitted
                    </h5>
                    <span class="badge bg-light text-dark border">{{ $attendance->attendances->count() }}</span>
                </div>
                <div class="list-group list-group-flush overflow-auto" style="max-height: 600px;">
                    @forelse($attendance->attendances as $record)
                        <div class="list-group-item px-4 py-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-{{ $record->getStatusColor() == 'green' ? 'success' : ($record->getStatusColor() == 'yellow' ? 'warning' : 'danger') }} bg-opacity-10 text-{{ $record->getStatusColor() == 'green' ? 'success' : ($record->getStatusColor() == 'yellow' ? 'warning' : 'danger') }} d-flex align-items-center justify-content-center fw-bold me-3" style="width: 40px; height: 40px;">
                                        {{ substr($record->participant->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">
                                            {{ $record->participant->name }}
                                            @if($record->participant->id_number)
                                                <span class="badge bg-light text-dark border ms-1 font-monospace small">{{ $record->participant->id_number }}</span>
                                            @endif
                                        </h6>
                                        <small class="text-muted">{{ $record->submitted_at->format('M d, H:i') }}</small>
                                    </div>
                                </div>
                                <span class="badge rounded-pill bg-{{ $record->getStatusColor() == 'green' ? 'success' : ($record->getStatusColor() == 'yellow' ? 'warning' : 'danger') }} bg-opacity-10 text-{{ $record->getStatusColor() == 'green' ? 'success' : ($record->getStatusColor() == 'yellow' ? 'warning' : 'danger') }} border border-{{ $record->getStatusColor() == 'green' ? 'success' : ($record->getStatusColor() == 'yellow' ? 'warning' : 'danger') }} border-opacity-25">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </div>

                            @if($record->note)
                                <div class="bg-light p-2 rounded small text-muted fst-italic ms-5 mb-2 border">
                                    "{{ $record->note }}"
                                </div>
                            @endif

                             @if($record->proof_file_path)
                                <div class="ms-5 mt-1">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('files.attendance-proof', $record) }}" target="_blank" class="btn btn-outline-success">
                                            <i class="bi bi-eye me-1"></i> View Proof
                                        </a>
                                        <a href="{{ route('files.attendance-proof', ['attendance' => $record, 'action' => 'download']) }}" class="btn btn-outline-primary">
                                            <i class="bi bi-download me-1"></i> Download
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <p class="mb-0">No submissions yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Not Submitted -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold mb-0 text-warning">
                        <i class="bi bi-clock-history me-2"></i> Pending Submission
                    </h5>
                    <span class="badge bg-light text-dark border">{{ $notSubmitted->count() }}</span>
                </div>
                <div class="list-group list-group-flush overflow-auto" style="max-height: 600px;">
                    @forelse($notSubmitted as $participant)
                        <div class="list-group-item px-4 py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
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

                                <!-- Manual Mark Form -->
                                <form action="{{ route('mentor.attendance.mark', $attendance) }}" method="POST" class="d-flex align-items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="participant_id" value="{{ $participant->id }}">
                                    <select name="status" class="form-select form-select-sm" style="width: auto;">
                                        <option value="present">Present</option>
                                        <option value="permission">Permission</option>
                                        <option value="sick">Sick</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-dark">Mark</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="display-1">🎉</div>
                            <p class="fw-bold mt-2">All completed!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
