<x-app-layout>
    @section('title', 'Class Details')

    <!-- Class Header -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <div class="d-md-flex justify-content-between align-items-start">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <h1 class="h3 fw-bold mb-0 me-3">{{ $class->name }}</h1>
                        <span class="badge bg-primary fs-6">{{ $class->code }}</span>
                    </div>
                    <p class="text-muted mb-0">{{ $class->description ?? 'No description provided.' }}</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-outline-primary btn-sm me-2">
                        <i class="bi bi-pencil me-1"></i> Edit Class
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Mentor Info -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title fw-bold mb-0">Assigned Mentor</h5>
                </div>
                <div class="card-body text-center pt-0 pb-4">
                    @if($class->mentor)
                        <div class="mb-3">
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center p-3 mb-2" style="width: 80px; height: 80px;">
                                <i class="bi bi-person-badge fs-1 text-primary"></i>
                            </div>
                            <h5 class="fw-bold mb-1">{{ $class->mentor->name }}</h5>
                            <p class="text-muted small mb-0">{{ $class->mentor->email }}</p>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Active Mentor</span>
                    @else
                        <div class="py-4 text-muted">
                            <i class="bi bi-person-x fs-1 mb-2"></i>
                            <p>No mentor assigned</p>
                             <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-sm btn-outline-primary mt-2">Assign Mentor</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Participants List -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold mb-0">Members ({{ $class->participants->count() }})</h5>
                    
                    <!-- Add Member Form Trigger -->
                    <button type="button" class="btn btn-sm btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Member
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 ps-4">Name</th>
                                <th class="border-0">Email</th>
                                <th class="border-0 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($class->participants as $participant)
                                <tr>
                                    <td class="ps-4 fw-medium">{{ $participant->name }}</td>
                                    <td class="text-muted">{{ $participant->email }}</td>
                                    <td class="text-end pe-4">
                                        <form action="{{ route('admin.classes.removeMember', [$class, $participant]) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Remove this user from the class?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0" title="Remove">
                                                <i class="bi bi-x-circle-fill"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No members in this class yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Add Members to Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.classes.addMember', $class) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @if($availableParticipants->count() > 0)
                            <!-- Search Filter -->
                            <div class="mb-3">
                                <input type="text" id="memberSearch" class="form-control" placeholder="Search by name or email..." oninput="filterMembers()">
                            </div>

                            <!-- Select All / Deselect All -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small"><span id="selectedCount">0</span> selected</span>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">Select All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">Deselect All</button>
                                </div>
                            </div>

                            <!-- Participant List -->
                            <div class="border rounded" style="max-height: 350px; overflow-y: auto;">
                                <div class="list-group list-group-flush" id="memberList">
                                    @foreach($availableParticipants as $user)
                                        <label class="list-group-item list-group-item-action d-flex align-items-center member-item" data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}">
                                            <input type="checkbox" class="form-check-input me-3 member-checkbox" name="user_ids[]" value="{{ $user->id }}" onchange="updateSelectedCount()">
                                            <div class="flex-grow-1">
                                                <div class="fw-medium">{{ $user->name }}</div>
                                                <div class="text-muted small">{{ $user->email }}</div>
                                            </div>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">Participant</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-text mt-2">Only showing participants not currently assigned to any class.</div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-people fs-1 mb-2"></i>
                                <p class="mb-0">No available participants to add.</p>
                                <small>All participants are already assigned to a class.</small>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        @if($availableParticipants->count() > 0)
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i> Add Selected Members
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function filterMembers() {
            const search = document.getElementById('memberSearch').value.toLowerCase();
            const items = document.querySelectorAll('.member-item');
            
            items.forEach(item => {
                const name = item.getAttribute('data-name');
                const email = item.getAttribute('data-email');
                if (name.includes(search) || email.includes(search)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function selectAll() {
            const visibleCheckboxes = document.querySelectorAll('.member-item:not([style*="display: none"]) .member-checkbox');
            visibleCheckboxes.forEach(cb => cb.checked = true);
            updateSelectedCount();
        }

        function deselectAll() {
            const checkboxes = document.querySelectorAll('.member-checkbox');
            checkboxes.forEach(cb => cb.checked = false);
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const count = document.querySelectorAll('.member-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = count;
        }
    </script>
</x-app-layout>
