<x-app-layout>
    @section('title', 'Attendance Sessions')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Attendance Sessions</h1>
            @if($class)
                <p class="text-muted small">
                    Managing attendance for <span class="fw-bold text-primary">{{ $class->name }}</span>
                </p>
            @else
                <p class="text-danger small mb-0"><i class="bi bi-exclamation-triangle-fill me-1"></i> You are not assigned to any class.</p>
            @endif
        </div>
        @if($class)
            <a href="{{ route('mentor.attendance.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Create Session
            </a>
        @endif
    </div>

    @if($class)
        <!-- Class Info Card -->
        <div class="card bg-primary bg-gradient text-white shadow mb-4 border-0">
            <div class="card-body p-4">
                <div class="d-md-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1 fw-bold">{{ $class->name }}</h4>
                        <div class="d-flex align-items-center text-white-50">
                            <span class="me-3"><i class="bi bi-qr-code me-1"></i> {{ $class->code }}</span>
                            <span><i class="bi bi-people-fill me-1"></i> {{ $class->participants->count() }} Participants</span>
                        </div>
                    </div>
                    <form action="{{ route('mentor.attendance.index') }}" method="GET" class="mt-3 mt-md-0">
                        <select name="status" onchange="this.form.submit()" class="form-select bg-white bg-opacity-10 text-white border-0">
                            <option value="" class="text-dark">All Status</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }} class="text-dark">Open</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }} class="text-dark">Closed</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sessions Table -->
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 border-0">Session</th>
                            <th class="py-3 border-0">Schedule</th>
                            <th class="py-3 border-0">Status</th>
                            <th class="pe-4 py-3 border-0 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $session->title }}</div>
                                    @if($session->description)
                                        <div class="small text-muted text-truncate" style="max-width: 200px;">
                                            {{ Str::limit($session->description, 50) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column text-muted small">
                                        <span class="fw-medium text-dark">{{ $session->open_at->format('M d, Y') }}</span>
                                        <span>{{ $session->open_at->format('H:i') }} - {{ $session->close_at->format('H:i') }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($session->is_open)
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill border border-success border-opacity-25">
                                            <span class="spinner-grow spinner-grow-sm me-1" style="width: 0.4rem; height: 0.4rem;"></span> Open
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill border border-secondary border-opacity-25">Closed</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('mentor.attendance.show', $session) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('mentor.attendance.edit', $session) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="{{ route('mentor.attendance.toggle', $session) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $session->is_open ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $session->is_open ? 'Close Session' : 'Open Session' }}">
                                                <i class="bi {{ $session->is_open ? 'bi-stop-circle-fill' : 'bi-play-circle-fill' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('mentor.attendance.destroy', $session) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this session?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">No attendance sessions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

         <div class="mt-4">
            {{ $sessions->links() }}
        </div>
    @else
        <div class="alert alert-warning border-0 shadow-sm rounded-3 p-4 text-center">
            <i class="bi bi-exclamation-circle fs-1 text-warning mb-2 d-block"></i>
            <h4 class="alert-heading fw-bold">No Class Assigned</h4>
            <p class="mb-0">You are not assigned to any class yet. Please contact an administrator.</p>
        </div>
    @endif
</x-app-layout>
