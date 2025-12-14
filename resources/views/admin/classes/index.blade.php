<x-app-layout>
    @section('title', 'Class Management')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Class Groups</h1>
            <p class="text-muted small">Manage class groups, memberships, and assignments.</p>
        </div>
        <a href="{{ route('admin.classes.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-lg me-2"></i> Create Class
        </a>
    </div>

    <!-- Search -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form action="{{ route('admin.classes.index') }}" method="GET" class="d-flex gap-2">
                <div class="flex-grow-1 position-relative">
                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control rounded-pill ps-5" placeholder="Search by name or code...">
                </div>
                <button type="submit" class="btn btn-dark rounded-pill px-4">Search</button>
            </form>
        </div>
    </div>

    <!-- Classes Grid -->
    <div class="row g-4">
        @forelse($classes as $class)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 hover-shadow transition">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title fw-bold mb-1">{{ $class->name }}</h5>
                                <code class="text-primary fw-bold">{{ $class->code }}</code>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill">
                                {{ $class->memberships_count }} members
                            </span>
                        </div>
                        
                        <p class="card-text text-muted small mb-4" style="min-height: 48px;">
                            {{ $class->description ? Str::limit($class->description, 100) : 'No description provided for this class group.' }}
                        </p>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('admin.classes.show', $class) }}" class="text-decoration-none fw-bold small">
                                View Details <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                            <div class="btn-group">
                                <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-light btn-sm rounded-circle shadow-sm me-1" title="Edit">
                                    <i class="bi bi-pencil-fill text-muted"></i>
                                </a>
                                <form action="{{ route('admin.classes.destroy', $class) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure? This will delete all associated data.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm rounded-circle shadow-sm" title="Delete">
                                        <i class="bi bi-trash-fill text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 py-5 text-center">
                <div class="bg-light rounded-circle p-4 d-inline-block mb-3">
                    <i class="bi bi-mortarboard text-secondary fs-1"></i>
                </div>
                <h4>No classes found</h4>
                <p class="text-muted">Get started by creating your first class group.</p>
                <a href="{{ route('admin.classes.create') }}" class="btn btn-primary rounded-pill px-4">
                    Create Class Group
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $classes->links() }}
    </div>
</x-app-layout>
