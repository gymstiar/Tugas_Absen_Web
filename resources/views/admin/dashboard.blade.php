<x-app-layout>
    @section('title', 'Admin Dashboard')

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
        <!-- Total Users -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary me-3">
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">Total Users</h6>
                            <h3 class="card-title mb-0 fw-bold">{{ $stats['total_users'] }}</h3>
                        </div>
                    </div>
                    <div class="small text-muted d-flex justify-content-between">
                        <span><i class="bi bi-circle-fill text-success" style="font-size: 8px;"></i> {{ $stats['total_admins'] }} Admin</span>
                        <span><i class="bi bi-circle-fill text-primary" style="font-size: 8px;"></i> {{ $stats['total_mentors'] }} Mentor</span>
                        <span><i class="bi bi-circle-fill text-secondary" style="font-size: 8px;"></i> {{ $stats['total_participants'] }} Student</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class Groups -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success me-3">
                            <i class="bi bi-mortarboard-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">Class Groups</h6>
                            <h3 class="card-title mb-0 fw-bold">{{ $stats['total_classes'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Sessions -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning me-3">
                            <i class="bi bi-calendar-check-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">Attendance</h6>
                            <h3 class="card-title mb-0 fw-bold">{{ $stats['total_attendance_sessions'] }}</h3>
                        </div>
                    </div>
                    <div>
                        <span class="badge rounded-pill {{ $stats['active_sessions'] > 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }}">
                            {{ $stats['active_sessions'] }} Active Now
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Tasks -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info me-3">
                            <i class="bi bi-clipboard-data-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">Total Tasks</h6>
                            <h3 class="card-title mb-0 fw-bold">{{ $stats['total_tasks'] }}</h3>
                        </div>
                    </div>
                    <div class="text-muted small">
                        {{ $stats['total_submissions'] }} Submissions
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="row g-4">
        <!-- Recent Users -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">Recent Users</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">View all</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentUsers as $user)
                            <div class="list-group-item px-4 py-3 d-flex align-items-center border-bottom-0">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-primary me-3" style="width: 40px; height: 40px;">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-semibold">{{ $user->name }}</h6>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                                <span class="badge rounded-pill {{ $user->role === 'admin' ? 'bg-danger' : ($user->role === 'mentor' ? 'bg-primary' : 'bg-success') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">No users found</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Attendance Sessions -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0 fw-bold">Active Attendance Sessions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($activeSessions as $session)
                            <div class="list-group-item px-4 py-3 border-bottom-0">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h6 class="mb-0 fw-semibold">{{ $session->title }}</h6>
                                        <span class="badge bg-light text-dark border">{{ $session->classGroup->name }}</span>
                                    </div>
                                    <span class="badge bg-success bg-opacity-75">
                                        <span class="spinner-grow spinner-grow-sm me-1" role="status" aria-hidden="true" style="width: 0.5rem; height: 0.5rem;"></span>
                                        Open
                                    </span>
                                </div>
                                <div class="text-muted small">
                                    By {{ $session->mentor->name }} • Closes {{ $session->close_at->diffForHumans() }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                                No active sessions
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-4">
        <h5 class="fw-bold mb-3">Quick Actions</h5>
        <div class="row g-3">
            <div class="col-sm-6 col-lg-2">
                <a href="{{ route('admin.users.create') }}" class="card text-decoration-none shadow-sm h-100 border-0 hover-shadow transition">
                    <div class="card-body text-center p-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 d-inline-block mb-2 text-primary">
                            <i class="bi bi-person-plus-fill fs-5"></i>
                        </div>
                        <h6 class="text-dark mb-0">Add User</h6>
                    </div>
                </a>
            </div>
            
            <div class="col-sm-6 col-lg-2">
                <a href="{{ route('admin.classes.create') }}" class="card text-decoration-none shadow-sm h-100 border-0 hover-shadow transition">
                    <div class="card-body text-center p-3">
                        <div class="rounded-circle bg-success bg-opacity-10 p-2 d-inline-block mb-2 text-success">
                            <i class="bi bi-plus-lg fs-5"></i>
                        </div>
                        <h6 class="text-dark mb-0">Create Class</h6>
                    </div>
                </a>
            </div>
            
            <div class="col-sm-6 col-lg-2">
                <a href="{{ route('admin.reports.attendance') }}" class="card text-decoration-none shadow-sm h-100 border-0 hover-shadow transition">
                    <div class="card-body text-center p-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-2 d-inline-block mb-2 text-warning">
                            <i class="bi bi-calendar-range fs-5"></i>
                        </div>
                        <h6 class="text-dark mb-0">Attendance Report</h6>
                    </div>
                </a>
            </div>
            
            <div class="col-sm-6 col-lg-2">
                <a href="{{ route('admin.reports.tasks') }}" class="card text-decoration-none shadow-sm h-100 border-0 hover-shadow transition">
                    <div class="card-body text-center p-3">
                        <div class="rounded-circle bg-info bg-opacity-10 p-2 d-inline-block mb-2 text-info">
                            <i class="bi bi-list-task fs-5"></i>
                        </div>
                        <h6 class="text-dark mb-0">Tasks Report</h6>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-lg-2">
                <a href="{{ route('admin.reports.printable') }}" target="_blank" class="card text-decoration-none shadow-sm h-100 border-0 bg-danger bg-gradient text-white hover-shadow transition">
                    <div class="card-body text-center p-3">
                        <div class="rounded-circle bg-white bg-opacity-25 p-2 d-inline-block mb-2 text-white">
                            <i class="bi bi-printer-fill fs-5"></i>
                        </div>
                        <h6 class="text-white mb-0">Print Report</h6>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-lg-2">
                <a href="{{ route('admin.reports.exportFullCsv') }}" class="card text-decoration-none shadow-sm h-100 border-0 bg-success bg-gradient text-white hover-shadow transition">
                    <div class="card-body text-center p-3">
                        <div class="rounded-circle bg-white bg-opacity-25 p-2 d-inline-block mb-2 text-white">
                            <i class="bi bi-file-earmark-spreadsheet-fill fs-5"></i>
                        </div>
                        <h6 class="text-white mb-0">CSV Report</h6>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
