<x-app-layout>
    @section('title', 'User Management')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Users</h1>
            <p class="text-muted small">Manage system users, roles, and access.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-person-plus-fill me-2"></i> Add New User
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-6 col-lg-5">
                    <label for="search" class="form-label small fw-bold text-muted">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Search by name or email...">
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <label for="role" class="form-label small fw-bold text-muted">Role</label>
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="mentor" {{ request('role') == 'mentor' ? 'selected' : '' }}>Mentor</option>
                        <option value="participant" {{ request('role') == 'participant' ? 'selected' : '' }}>Participant</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">Filter</button>
                </div>
                @if(request()->hasAny(['search', 'role']))
                    <div class="col-md-auto">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0">User</th>
                        <th class="py-3 border-0">ID Number</th>
                        <th class="py-3 border-0">Role</th>
                        <th class="py-3 border-0">Joined</th>
                        <th class="pe-4 py-3 border-0 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-gradient text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 40px; height: 40px;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="ms-3">
                                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                                        <div class="small text-muted">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->id_number)
                                    <span class="font-monospace fw-semibold">{{ $user->id_number }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $user->role === 'admin' ? 'bg-danger bg-opacity-10 text-danger' : ($user->role === 'mentor' ? 'bg-primary bg-opacity-10 text-primary' : 'bg-success bg-opacity-10 text-success') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-search fs-1 text-muted mb-2"></i>
                                    <h5 class="text-muted">No users found</h5>
                                    <p class="text-muted small">Try adjusting your filters or search.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</x-app-layout>
