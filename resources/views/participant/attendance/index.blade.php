<x-app-layout>
    @section('title', 'My Attendance')

    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">My Attendance</h1>
        <p class="text-muted small">Track your attendance history and submit for active sessions.</p>
    </div>

    @if(!$class)
        <div class="alert alert-warning border-0 shadow-sm mb-4">
            <div class="d-flex">
                <div class="display-6 me-3">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <h5 class="alert-heading fw-bold">No Class Assigned</h5>
                    <p class="mb-0">You have not been assigned to a class yet. You cannot track attendance or submit tasks until you are added to a class.</p>
                    <hr>
                    <p class="mb-0 small">Please contact your administrator to be assigned to a class.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Active Sessions -->
    @if($activeSessions->count() > 0)
        <div class="card bg-primary bg-gradient text-white shadow mb-4 border-0">
            <div class="card-body p-4">
                <h5 class="card-title fw-bold mb-3 d-flex align-items-center">
                    <span class="bg-white bg-opacity-25 p-1 rounded me-2">
                        <i class="bi bi-clock-history"></i>
                    </span>
                    Active Sessions - Action Required
                </h5>
                <div class="vstack gap-3">
                    @foreach($activeSessions as $session)
                        <div class="card bg-white bg-opacity-10 border-0">
                            <div class="card-body p-3">
                                <div class="d-md-flex justify-content-between align-items-center">
                                    <div class="mb-3 mb-md-0">
                                        <h5 class="mb-1 fw-bold">{{ $session->title }}</h5>
                                        <div class="text-white-50 small">
                                            <span class="fw-bold">{{ $session->classGroup->name }}</span>
                                            <span class="mx-2">•</span>
                                            <span>Closes {{ $session->close_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    @if($session->has_submitted)
                                        <div class="d-flex align-items-center border border-success border-opacity-50 bg-success bg-opacity-25 px-3 py-2 rounded">
                                            <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                                            <div class="text-end lh-1">
                                                <div class="fw-bold small">Submitted</div>
                                                <small class="text-success-emphasis" style="font-size: 0.75rem;">{{ ucfirst($session->submission->status) }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <a href="{{ route('participant.attendance.show', $session) }}" class="btn btn-light fw-bold text-primary shadow-sm text-nowrap">
                                            Submit Attendance <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Past Sessions -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light py-3">
             <h6 class="card-title fw-bold mb-0 text-dark">Attendance History</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-white">
                    <tr>
                        <th class="ps-4 py-3 border-0">Session</th>
                        <th class="py-3 border-0">Class</th>
                        <th class="py-3 border-0">Date</th>
                        <th class="pe-4 py-3 border-0">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pastSessions as $session)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $session->title }}</td>
                            <td class="text-muted small">{{ $session->classGroup->name }}</td>
                            <td class="text-muted small">{{ $session->open_at->format('M d, Y') }}</td>
                            <td class="pe-4">
                                @if($session->submission)
                                    <span class="badge rounded-pill {{ $session->submission->status === 'present' ? 'bg-success bg-opacity-10 text-success' : ($session->submission->status === 'sick' ? 'bg-danger bg-opacity-10 text-danger' : 'bg-warning bg-opacity-10 text-warning') }}">
                                        {{ ucfirst($session->submission->status) }}
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-light text-muted border">Absent</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No past attendance records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $pastSessions->links() }}</div>
</x-app-layout>
