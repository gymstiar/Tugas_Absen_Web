<x-app-layout>
    @section('title', 'My Tasks')

    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">My Tasks</h1>
        <p class="text-muted small">Manage your assignments and track your progress.</p>
    </div>

    @if($classes->isEmpty())
        <div class="alert alert-warning border-0 shadow-sm mb-4">
            <div class="d-flex">
                <div class="display-6 me-3">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <h5 class="alert-heading fw-bold">No Class Assigned</h5>
                    <p class="mb-0">You have not been assigned to a class yet. You cannot view or submit tasks until you are added to a class.</p>
                    <hr>
                    <p class="mb-0 small">Please contact your administrator to be assigned to a class.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form action="{{ route('participant.tasks.index') }}" method="GET" class="row g-3 align-items-end">
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
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tasks List -->
    <div class="vstack gap-3">
        @forelse($tasks as $task)
            <div class="card shadow-sm border-0 hover-shadow transition">
                <div class="card-body">
                    <div class="row g-3 align-items-start">
                        <div class="col-md-9">
                            <div class="mb-2">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 me-2">
                                    {{ $task->classGroup->name }}
                                </span>
                                <small class="text-muted">
                                    <i class="bi bi-person-circle me-1"></i> {{ $task->mentor->name }}
                                </small>
                            </div>
                            <h5 class="fw-bold mb-2">
                                <a href="{{ route('participant.tasks.show', $task) }}" class="text-decoration-none text-dark hover-primary stretched-link">
                                    {{ $task->title }}
                                </a>
                            </h5>
                            @if($task->description)
                                <p class="text-muted small mb-0 text-truncate" style="max-width: 90%;">{{ $task->description }}</p>
                            @endif
                        </div>
                        <div class="col-md-3 text-md-end position-relative z-2">
                             @if($task->submission)
                                @if($task->submission->isGraded())
                                    <span class="badge bg-{{ $task->submission->getGradeColor() == 'green' ? 'success' : ($task->submission->getGradeColor() == 'yellow' ? 'warning' : 'danger') }} bg-opacity-10 text-{{ $task->submission->getGradeColor() == 'green' ? 'success' : ($task->submission->getGradeColor() == 'yellow' ? 'warning' : 'danger') }} border border-{{ $task->submission->getGradeColor() == 'green' ? 'success' : ($task->submission->getGradeColor() == 'yellow' ? 'warning' : 'danger') }} border-opacity-25 rounded-pill px-3 py-2">
                                        Grade: {{ $task->submission->grade }}
                                    </span>
                                @else
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-2">
                                        <i class="bi bi-check-lg me-1"></i> Submitted
                                    </span>
                                @endif
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-2">
                                    Pending
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-2 border-top-0 d-flex justify-content-between align-items-center">
                    <small class="{{ $task->isPastDue() ? 'text-danger fw-bold' : 'text-muted' }}">
                        <i class="bi bi-calendar-event me-1"></i> Due: {{ $task->due_date->format('M d, Y H:i') }}
                         @if($task->isPastDue()) 
                            <span class="badge bg-danger ms-2">Overdue</span>
                        @endif
                    </small>
                    <a href="{{ route('participant.tasks.show', $task) }}" class="btn btn-primary btn-sm rounded-pill px-3 position-relative z-2">
                        View Details
                    </a>
                </div>
            </div>
        @empty
            <div class="alert alert-light text-center border-0 shadow-sm p-5">
                <i class="bi bi-check2-circle display-4 text-muted mb-3 d-block"></i>
                <h5 class="fw-bold text-muted">No tasks found</h5>
                <p class="mb-0 text-muted small">You're all caught up!</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $tasks->links() }}</div>
</x-app-layout>
