<x-app-layout>
    @section('title', 'Reports')

    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Reports Dashboard</h1>
        <p class="text-muted small">View summaries and access detailed reports</p>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-lg">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-1 text-primary mb-2"></i>
                    <h2 class="display-5 fw-bold text-dark mb-0">{{ $stats['total_participants'] }}</h2>
                    <p class="text-muted small mb-0">Participants</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-person-badge fs-1 text-success mb-2"></i>
                    <h2 class="display-5 fw-bold text-dark mb-0">{{ $stats['total_mentors'] }}</h2>
                    <p class="text-muted small mb-0">Mentors</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-collection fs-1 text-info mb-2"></i>
                    <h2 class="display-5 fw-bold text-dark mb-0">{{ $stats['total_classes'] }}</h2>
                    <p class="text-muted small mb-0">Classes</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check fs-1 text-warning mb-2"></i>
                    <h2 class="display-5 fw-bold text-dark mb-0">{{ $stats['total_sessions'] }}</h2>
                    <p class="text-muted small mb-0">Sessions</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-file-earmark-text fs-1 text-danger mb-2"></i>
                    <h2 class="display-5 fw-bold text-dark mb-0">{{ $stats['total_tasks'] }}</h2>
                    <p class="text-muted small mb-0">Tasks</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Links -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start mb-3">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="bi bi-clipboard-check fs-3 text-success"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Attendance Report</h5>
                            <p class="text-muted small mb-0">View and export attendance records by class and date</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.reports.attendance') }}" class="btn btn-success w-100">
                        <i class="bi bi-arrow-right me-1"></i> View Attendance Report
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start mb-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="bi bi-journal-text fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Task Submissions Report</h5>
                            <p class="text-muted small mb-0">View and export task submission records and grades</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.reports.tasks') }}" class="btn btn-primary w-100">
                        <i class="bi bi-arrow-right me-1"></i> View Tasks Report
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start mb-3">
                        <div class="bg-dark bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="bi bi-printer fs-3 text-dark"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Printable Report</h5>
                            <p class="text-muted small mb-0">Generate a printable summary report (PDF via browser print)</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.reports.printable') }}" target="_blank" class="btn btn-dark w-100">
                        <i class="bi bi-file-earmark-pdf me-1"></i> View Printable Report
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start mb-3">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="bi bi-file-earmark-spreadsheet fs-3 text-info"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Full CSV Export</h5>
                            <p class="text-muted small mb-0">Export all data (summary, classes, attendance, tasks) as CSV</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.reports.exportFullCsv') }}" class="btn btn-info w-100 text-white">
                        <i class="bi bi-download me-1"></i> Download Full CSV
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
