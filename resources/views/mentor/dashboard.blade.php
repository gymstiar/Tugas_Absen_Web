<x-app-layout>
    @section('title', 'Mentor Dashboard')

    <!-- Header & Clock -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start mb-4 gap-3">
        <h4 class="mb-0 fw-bold text-dark">Dashboard Overview</h4>
        <div class="bg-white rounded shadow-sm p-2 px-3 d-flex align-items-center border w-100 w-md-auto justify-content-between justify-content-md-start">
            <div class="d-flex align-items-center">
                <i class="bi bi-clock text-primary me-2"></i>
                <div class="text-end" style="line-height: 1.2;">
                    <div id="clock-time" class="fw-bold font-monospace text-dark fs-5">--:--:--</div>
                    <div id="clock-date" class="small text-muted" style="font-size: 0.75rem;">Loading...</div>
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
                            <i class="bi bi-calendar-check fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">Active Sessions</h6>
                            <h3 class="card-title mb-0 fw-bold">{{ $stats['active_sessions'] }}</h3>
                        </div>
                    </div>
                    <div class="text-muted small">
                        {{ $stats['total_sessions'] }} Total Sessions
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info me-3">
                            <i class="bi bi-clipboard-check fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">Total Tasks</h6>
                            <h3 class="card-title mb-0 fw-bold">{{ $stats['total_tasks'] }}</h3>
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
                            <i class="bi bi-mortarboard fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">Pending Grades</h6>
                            <h3 class="card-title mb-0 fw-bold">{{ $stats['pending_submissions'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="row g-4">
        <!-- Active Attendance Sessions -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">Active Sessions</h5>
                    <a href="{{ route('mentor.attendance.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> New Session
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($activeSessions as $session)
                            <div class="list-group-item px-4 py-3 border-bottom-0">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <a href="{{ route('mentor.attendance.show', $session) }}" class="fw-semibold text-decoration-none text-dark stretched-link">
                                            {{ $session->title }}
                                        </a>
                                        <div class="small text-muted">{{ $session->classGroup->name }}</div>
                                    </div>
                                    <div class="d-flex align-items-center z-2">
                                        <span class="badge bg-success bg-opacity-75 me-2">
                                            <span class="spinner-grow spinner-grow-sm me-1" style="width: 0.5rem; height: 0.5rem;"></span>
                                            Open
                                        </span>
                                        <form action="{{ route('mentor.attendance.toggle', $session) }}" method="POST" class="d-inline z-3" style="position: relative; z-index: 10;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size: 0.75rem;">Close</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="text-muted small">
                                    Closes {{ $session->close_at->diffForHumans() }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <p class="mb-2">No active sessions</p>
                                <a href="{{ route('mentor.attendance.create') }}" class="btn btn-sm btn-outline-primary">Create one now</a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Sessions -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0 fw-bold">Upcoming Sessions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($upcomingSessions as $session)
                            <div class="list-group-item px-4 py-3 border-bottom-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 fw-semibold">{{ $session->title }}</h6>
                                        <small class="text-muted">{{ $session->classGroup->name }}</small>
                                    </div>
                                    <span class="badge bg-secondary">
                                        {{ $session->open_at->format('M d, H:i') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">No upcoming sessions</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Tasks -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">Recent Tasks</h5>
                    <a href="{{ route('mentor.tasks.index') }}" class="btn btn-sm btn-outline-primary">View all</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentTasks as $task)
                            <div class="list-group-item px-4 py-3 border-bottom-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('mentor.tasks.show', $task) }}" class="fw-semibold text-decoration-none text-dark">
                                            {{ $task->title }}
                                        </a>
                                        <div class="small text-muted">{{ $task->classGroup->name }}</div>
                                    </div>
                                    <span class="badge {{ $task->isPastDue() ? 'bg-danger' : 'bg-secondary' }}">
                                        Due {{ $task->due_date->format('M d') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">No tasks created yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Submissions -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0 fw-bold">Pending Submissions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($pendingSubmissions as $submission)
                            <div class="list-group-item px-4 py-3 border-bottom-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 fw-semibold">{{ $submission->participant->name }}</h6>
                                        <small class="text-muted">{{ $submission->task->title }}</small>
                                    </div>
                                    <a href="{{ route('mentor.tasks.show', $submission->task) }}" class="btn btn-sm btn-outline-primary">
                                        Grade
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">No pending submissions</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- My Students (Class Participants) -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-people-fill text-primary me-2"></i>My Students
                        @if($class)
                            <span class="badge bg-primary bg-opacity-10 text-primary ms-2">{{ $participants->count() }}</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($participants->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3 border-0">#</th>
                                        <th class="py-3 border-0">Student</th>
                                        <th class="py-3 border-0">ID Number</th>
                                        <th class="py-3 border-0">Email</th>
                                        <th class="py-3 border-0">Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($participants as $index => $participant)
                                        <tr>
                                            <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fw-bold me-3" style="width: 36px; height: 36px;">
                                                        {{ substr($participant->name, 0, 1) }}
                                                    </div>
                                                    <span class="fw-semibold">{{ $participant->name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if($participant->id_number)
                                                    <span class="font-monospace fw-semibold">{{ $participant->id_number }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-muted">{{ $participant->email }}</td>
                                            <td class="text-muted small">{{ $participant->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 mb-2 d-block"></i>
                            <p class="mb-0">No students in this class yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
