<x-app-layout>
    @section('title', 'Submit Attendance')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-3">
                <a href="{{ route('participant.attendance.index') }}" class="text-decoration-none text-muted small fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Attendance
                </a>
            </div>

            <!-- Session Info Card -->
            <div class="card shadow-sm border-0 overflow-hidden mb-4">
                <div class="card-header bg-primary bg-gradient text-white py-3">
                    <h5 class="card-title fw-bold mb-0">{{ $session->title }}</h5>
                    <p class="mb-0 small text-white-50">{{ $session->classGroup->name }}</p>
                </div>
                <div class="card-body p-4">
                    @if($session->description)
                        <p class="text-muted mb-3">{{ $session->description }}</p>
                    @endif

                    <div class="d-flex gap-2">
                         <span class="badge bg-light text-dark border">
                            <i class="bi bi-calendar-event me-1"></i>
                            {{ $session->open_at->format('M d, Y H:i') }} - {{ $session->close_at->format('H:i') }}
                        </span>
                        @if($session->isCurrentlyOpen())
                             <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                <span class="spinner-grow spinner-grow-sm me-1" role="status" aria-hidden="true"></span>
                                Session Open
                            </span>
                        @else
                             <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                Session Closed
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            @if($submission)
                <!-- Already Submitted -->
                <div class="card border-success shadow-none mb-4" style="background-color: #f0fff4;">
                    <div class="card-body text-center p-5">
                         <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white mb-3 shadow-sm" style="width: 64px; height: 64px;">
                            <i class="bi bi-check-lg fs-2"></i>
                        </div>
                        <h3 class="h4 fw-bold text-success mb-2">Attendance Submitted!</h3>
                        <p class="text-success-emphasis small mb-4">Your attendance has been recorded</p>

                        <div class="bg-white rounded p-4 text-start shadow-sm mx-auto" style="max-width: 400px;">
                             <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted small fw-bold">Status</span>
                                <span class="badge rounded-pill bg-{{ $submission->getStatusColor() == 'green' ? 'success' : ($submission->getStatusColor() == 'yellow' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($submission->status) }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted small fw-bold">Submitted</span>
                                <span class="small fw-medium">{{ $submission->submitted_at->format('M d, Y H:i') }}</span>
                            </div>
                            @if($submission->note)
                                <div class="py-2 border-bottom">
                                    <div class="text-muted small fw-bold mb-1">Note</div>
                                    <div class="small">{{ $submission->note }}</div>
                                </div>
                            @endif
                            @if($submission->proof_file_path)
                                <div class="py-2 text-center mt-2">
                                     <a href="{{ route('participant.attendance.downloadProof', $submission) }}" class="btn btn-sm btn-outline-primary w-100">
                                        <i class="bi bi-file-earmark-image me-1"></i> View Proof
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @elseif($session->isCurrentlyOpen())
                <!-- Submit Form -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h5 class="card-title fw-bold mb-0">📋 Submit Your Attendance</h5>
                        <p class="text-muted small mb-0">Select your status and click submit</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <form action="{{ route('participant.attendance.submit', $session) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Status Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Select Your Status <span class="text-danger">*</span></label>
                                
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="status" id="status_present" value="present" required {{ old('status') == 'present' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-success w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center" for="status_present">
                                            <span class="fs-1 mb-2">✅</span>
                                            <span class="fw-bold">Present</span>
                                            <small class="text-muted">I'm attending</small>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="status" id="status_permission" value="permission" {{ old('status') == 'permission' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-warning w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center" for="status_permission">
                                            <span class="fs-1 mb-2">📝</span>
                                            <span class="fw-bold">Permission</span>
                                            <small class="text-muted">I have permission</small>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="status" id="status_sick" value="sick" {{ old('status') == 'sick' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-danger w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center" for="status_sick">
                                            <span class="fs-1 mb-2">🤒</span>
                                            <span class="fw-bold">Sick</span>
                                            <small class="text-muted">I'm not feeling well</small>
                                        </label>
                                    </div>
                                </div>
                                @error('status')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                            </div>

                            <!-- Note -->
                            <div class="mb-3">
                                <label for="note" class="form-label fw-bold">Note (Optional)</label>
                                <textarea name="note" id="note" rows="3" class="form-control" placeholder="Add any additional notes...">{{ old('note') }}</textarea>
                                <div class="form-text">Max 500 characters</div>
                            </div>

                            <!-- Proof Upload -->
                            <div class="mb-4 p-3 bg-light rounded border border-dashed">
                                <label for="proof_file" class="form-label fw-bold small text-uppercase text-muted">Proof File (For Permission/Sick)</label>
                                <input type="file" name="proof_file" id="proof_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                <div class="form-text">Max 3MB. Formats: JPG, PNG, PDF.</div>
                                @error('proof_file')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success p-3 fw-bold shadow-sm text-uppercase ls-1">
                                    Submit Attendance
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <!-- Session Closed -->
                <div class="alert alert-danger text-center shadow-sm border-0 p-5">
                     <i class="bi bi-slash-circle fs-1 text-danger mb-3 d-block"></i>
                    <h4 class="alert-heading fw-bold">Attendance Closed</h4>
                    <p class="mb-0">This attendance session is no longer accepting submissions.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
