<x-app-layout>
    @section('title', 'Participant Dashboard')

    <!-- Welcome Info Card -->
    <div class="card shadow-sm border-0 mb-4 bg-white overflow-hidden">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="fw-bold mb-1">Welcome back, {{ Auth::user()->name }}! 👋</h4>
                    <p class="text-muted mb-3">Here's what's happening in your class today.</p>
                    
                    @if($class)
                        <div class="d-flex flex-wrap gap-3">
                            @if(Auth::user()->id_number)
                                <div class="bg-light rounded px-3 py-2 border d-flex align-items-center">
                                    <i class="bi bi-person-vcard-fill text-info me-2"></i>
                                    <div>
                                        <small class="text-muted d-block" style="font-size: 0.7rem; line-height: 1;">ID NUMBER</small>
                                        <span class="fw-bold font-monospace">{{ Auth::user()->id_number }}</span>
                                    </div>
                                </div>
                            @endif
                            <div class="bg-light rounded px-3 py-2 border d-flex align-items-center">
                                <i class="bi bi-people-fill text-primary me-2"></i>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.7rem; line-height: 1;">CLASS</small>
                                    <span class="fw-bold">{{ $class->name }}</span> <span class="text-muted small">({{ $class->code }})</span>
                                </div>
                            </div>
                            <div class="bg-light rounded px-3 py-2 border d-flex align-items-center">
                                <i class="bi bi-person-badge-fill text-success me-2"></i>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.7rem; line-height: 1;">MENTOR</small>
                                    <span class="fw-bold">{{ $class->mentor ? $class->mentor->name : 'Unassigned' }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                         <div class="alert alert-warning mb-0 d-inline-block">
                            <i class="bi bi-exclamation-triangle me-1"></i> You are not assigned to any class yet.
                        </div>
                    @endif
                </div>
                <div class="col-md-4 col-12 mt-3 mt-md-0 d-flex justify-content-center align-items-center">
                    <div class="bg-light rounded p-3 d-flex align-items-center border w-100 justify-content-center">
                        <i class="bi bi-clock text-primary me-3 fs-3"></i>
                        <div class="text-end" style="line-height: 1.2;">
                            <div id="clock-time" class="fw-bold fs-4 font-monospace text-dark">--:--:--</div>
                            <div id="clock-date" class="small text-muted">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const timeOptions = { timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
            const dateOptions = { timeZone: 'Asia/Jakarta', weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' };
            
            document.getElementById('clock-time').textContent = new Intl.DateTimeFormat('en-GB', timeOptions).format(now);
            document.getElementById('clock-date').textContent = new Intl.DateTimeFormat('en-GB', dateOptions).format(now);
        }
        setInterval(updateClock, 1000);
        document.addEventListener('DOMContentLoaded', updateClock);
    </script>

    <!-- Stats Grid -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary me-3">
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">My Classes</h6>
                            <h3 class="card-title mb-0 fw-bold">{{ $stats['total_classes'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success me-3">
                            <i class="bi bi-check-circle-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">Attendances</h6>
                            <h3 class="card-title mb-0 fw-bold">{{ $stats['total_attendances'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info me-3">
                            <i class="bi bi-file-earmark-check-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">Submissions</h6>
                            <h3 class="card-title mb-0 fw-bold">{{ $stats['total_submissions'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning me-3">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">Pending Tasks</h6>
                            <h3 class="card-title mb-0 fw-bold">{{ $stats['pending_tasks'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Attendance Sessions -->
    @if($activeSessions->count() > 0)
        <div class="card border-0 shadow-sm bg-primary bg-gradient text-white mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-clipboard-check me-2"></i> Active Attendance Sessions</h5>
                <div class="d-grid gap-3">
                    @foreach($activeSessions as $session)
                        <div class="card bg-white bg-opacity-10 border-0">
                            <div class="card-body d-flex justify-content-between align-items-center p-3">
                                <div>
                                    <h6 class="mb-1 fw-bold">{{ $session->title }}</h6>
                                    <div class="text-white-50 small">{{ $session->classGroup->name }}</div>
                                    <div class="text-white-50 x-small mt-1">Closes {{ $session->close_at->diffForHumans() }}</div>
                                </div>
                                @if($session->has_submitted)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-lg me-1"></i> Submitted
                                    </span>
                                @else
                                    <a href="{{ route('participant.attendance.show', $session) }}" class="btn btn-light btn-sm fw-semibold text-primary">
                                        Submit Now <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Content Grid -->
    <div class="row g-4">
        <!-- Active Tasks -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">Active Tasks</h5>
                    <a href="{{ route('participant.tasks.index') }}" class="btn btn-sm btn-outline-primary">View all</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($activeTasks as $task)
                            <div class="list-group-item px-4 py-3 border-bottom-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="me-3">
                                        <a href="{{ route('participant.tasks.show', $task) }}" class="fw-semibold text-decoration-none text-dark d-block">
                                            {{ $task->title }}
                                        </a>
                                        <small class="text-muted d-block mb-2">{{ $task->classGroup->name }}</small>
                                        
                                        @if($task->has_submitted)
                                            @if($task->submission->isGraded())
                                                <span class="badge {{ $task->submission->getGradeColor() == 'green' ? 'bg-success' : ($task->submission->getGradeColor() == 'yellow' ? 'bg-warning' : 'bg-danger') }}">
                                                    Grade: {{ $task->submission->grade }}
                                                </span>
                                            @else
                                                <span class="badge bg-info">Submitted - Awaiting Grade</span>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="text-end" style="min-width: 100px;">
                                        @if($task->has_submitted)
                                            <span class="badge bg-success"><i class="bi bi-check-lg"></i> Submitted</span>
                                        @else
                                            <span class="badge {{ $task->isPastDue() ? 'bg-danger' : 'bg-secondary' }}">
                                                {{ $task->isPastDue() ? 'Overdue!' : 'Due ' . $task->due_date->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">No active tasks</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Submissions -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0 fw-bold">Recent Submissions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentSubmissions as $submission)
                            <div class="list-group-item px-4 py-3 border-bottom-0">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-0 fw-semibold">{{ $submission->task->title }}</h6>
                                        <small class="text-muted">{{ $submission->task->classGroup->name }}</small>
                                    </div>
                                    <div class="text-end">
                                        @if($submission->isGraded())
                                            <span class="badge {{ $submission->getGradeColor() == 'green' ? 'bg-success' : ($submission->getGradeColor() == 'yellow' ? 'bg-warning' : 'bg-danger') }}">
                                                {{ $submission->grade }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                        <div class="x-small text-muted mt-1">{{ $submission->submitted_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                                @if($submission->feedback)
                                    <div class="alert alert-light border mb-0 py-2 px-3 small">
                                        <strong class="text-dark">Feedback:</strong> {{ Str::limit($submission->feedback, 100) }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">No submissions yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Sessions -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0 fw-bold">Upcoming Attendance Sessions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($upcomingSessions as $session)
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold">{{ $session->title }}</h6>
                                        <p class="card-subtitle text-muted small mb-3">{{ $session->classGroup->name }}</p>
                                        
                                        <div class="small text-muted mb-1">
                                            <i class="bi bi-calendar me-2"></i> {{ $session->open_at->format('M d, Y') }}
                                        </div>
                                        <div class="small text-muted">
                                            <i class="bi bi-clock me-2"></i> {{ $session->open_at->format('H:i') }} - {{ $session->close_at->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4 text-muted">No upcoming sessions</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
