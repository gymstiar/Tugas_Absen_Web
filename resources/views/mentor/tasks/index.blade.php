<x-app-layout>
    @section('title', 'Tasks')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Tasks</h1>
            <p class="text-muted small">Create, manage, and grade tasks for your classes.</p>
        </div>
        <a href="{{ route('mentor.tasks.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-lg me-2"></i> Create New Task
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form action="{{ route('mentor.tasks.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="class_group_id" class="form-label small fw-bold text-muted">Class Group</label>
                    <select name="class_group_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_group_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="status" class="form-label small fw-bold text-muted">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0">Task Details</th>
                        <th class="py-3 border-0">Class</th>
                        <th class="py-3 border-0">Due Date</th>
                        <th class="py-3 border-0">Submissions</th>
                        <th class="pe-4 py-3 border-0 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $task->title }}</div>
                                <div class="mt-1">
                                    @if(!$task->is_active)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill">Inactive</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill">Active</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-muted fw-medium">{{ $task->classGroup->name }}</td>
                            <td>
                                <span class="{{ $task->isPastDue() ? 'text-danger fw-bold' : 'text-muted' }}">
                                    {{ $task->due_date->format('M d, Y H:i') }}
                                </span>
                                @if($task->isPastDue())
                                    <div class="text-danger small fw-bold">Overdue</div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold me-1 text-dark">{{ $task->submissions_count }}</span>
                                    <span class="text-muted small">submitted</span>
                                </div>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="btn-group">
                                    <a href="{{ route('mentor.tasks.show', $task) }}" class="btn btn-sm btn-outline-primary" title="View & Grade">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <form action="{{ route('mentor.tasks.toggle', $task) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $task->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $task->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="bi {{ $task->is_active ? 'bi-pause-circle-fill' : 'bi-play-circle-fill' }}"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('mentor.tasks.edit', $task) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form action="{{ route('mentor.tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this task? This cannot be undone and will delete all submissions.')">
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
                            <td colspan="5" class="text-center py-5 text-muted">No tasks found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $tasks->links() }}
    </div>
</x-app-layout>
