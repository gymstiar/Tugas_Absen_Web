<x-app-layout>
    @section('title', 'Attendance Report')

    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Attendance Report</h1>
        <p class="text-muted small">View and export attendance records</p>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form action="{{ route('admin.reports.attendance') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="class_group_id" class="form-label small fw-bold text-muted">Class Group</label>
                    <select name="class_group_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_group_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label small fw-bold text-muted">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label small fw-bold text-muted">To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-dark w-100">Filter</button>
                    <a href="{{ route('admin.reports.attendance.exportCsv', request()->query()) }}" class="btn btn-success w-100 text-nowrap">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0">Participant</th>
                        <th class="py-3 border-0">Class</th>
                        <th class="py-3 border-0">Session</th>
                        <th class="py-3 border-0">Status</th>
                        <th class="pe-4 py-3 border-0">Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $attendance->participant->name }}</div>
                                <div class="small text-muted">{{ $attendance->participant->email }}</div>
                            </td>
                            <td class="small text-muted">
                                {{ $attendance->attendanceSession->classGroup->name }}
                            </td>
                            <td class="text-dark fw-medium">
                                {{ $attendance->attendanceSession->title }}
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $attendance->status === 'present' ? 'bg-success bg-opacity-10 text-success' : ($attendance->status === 'sick' ? 'bg-danger bg-opacity-10 text-danger' : 'bg-warning bg-opacity-10 text-warning') }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>
                            <td class="pe-4 text-muted small">
                                {{ $attendance->submitted_at->format('M d, Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No attendance records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $attendances->links() }}
    </div>
</x-app-layout>
