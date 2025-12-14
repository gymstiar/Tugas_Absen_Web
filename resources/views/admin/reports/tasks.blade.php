<x-app-layout>
    @section('title', 'Tasks Report')

    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Task Submissions Report</h1>
        <p class="text-muted small">View and export task submission records</p>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form action="{{ route('admin.reports.tasks') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="class_group_id" class="form-label small fw-bold text-muted">Class Group</label>
                    <select name="class_group_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_group_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-dark w-100">Filter</button>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('admin.reports.tasks.exportCsv', request()->query()) }}" class="btn btn-success text-nowrap">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
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
                        <th class="py-3 border-0">Task</th>
                        <th class="py-3 border-0">Grade</th>
                        <th class="pe-4 py-3 border-0">Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $submission)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $submission->participant->name }}</div>
                                <div class="small text-muted">{{ $submission->participant->email }}</div>
                            </td>
                            <td class="small text-muted">
                                {{ $submission->task->classGroup->name }}
                            </td>
                            <td class="text-dark fw-medium">
                                {{ $submission->task->title }}
                            </td>
                            <td>
                                @if($submission->isGraded())
                                    <span class="badge rounded-pill {{ $submission->getGradeColor() == 'green' ? 'bg-success bg-opacity-10 text-success' : ($submission->getGradeColor() == 'yellow' ? 'bg-warning bg-opacity-10 text-warning' : 'bg-danger bg-opacity-10 text-danger') }}">
                                        {{ $submission->grade }}
                                    </span>
                                @else
                                    <span class="text-muted small fst-italic">Not graded</span>
                                @endif
                            </td>
                            <td class="pe-4 text-muted small">
                                {{ $submission->submitted_at->format('M d, Y H:i') }}
                                @if($submission->isLate())
                                    <span class="text-danger fw-bold ms-1">(Late)</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No submissions found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $submissions->links() }}
    </div>
</x-app-layout>
