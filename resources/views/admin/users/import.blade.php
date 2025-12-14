<x-app-layout>
    @section('title', 'Import Users')

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted small fw-bold">
                        <i class="bi bi-arrow-left me-1"></i> Back to Users
                    </a>
                    <h1 class="h3 fw-bold mt-2 mb-0">Import Users from CSV</h1>
                    <p class="text-muted small mb-0">Bulk import users using a CSV file</p>
                </div>
            </div>

            @if(isset($showResults) && $showResults)
                <!-- Import Results -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h5 class="card-title fw-bold mb-0"><i class="bi bi-clipboard-check me-2"></i>Import Results</h5>
                    </div>
                    <div class="card-body">
                        <!-- Summary Cards -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center">
                                        <i class="bi bi-file-earmark-text fs-2 text-primary mb-2"></i>
                                        <h3 class="fw-bold mb-0">{{ $results['total'] }}</h3>
                                        <p class="text-muted small mb-0">Total Rows</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success bg-opacity-10 border-0">
                                    <div class="card-body text-center">
                                        <i class="bi bi-check-circle fs-2 text-success mb-2"></i>
                                        <h3 class="fw-bold text-success mb-0">{{ $results['success'] }}</h3>
                                        <p class="text-muted small mb-0">Users Created</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-danger bg-opacity-10 border-0">
                                    <div class="card-body text-center">
                                        <i class="bi bi-x-circle fs-2 text-danger mb-2"></i>
                                        <h3 class="fw-bold text-danger mb-0">{{ $results['failed'] }}</h3>
                                        <p class="text-muted small mb-0">Failed Rows</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($results['success'] > 0)
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <strong>{{ $results['success'] }}</strong> users were successfully imported!
                            </div>
                        @endif

                        @if(count($results['errors']) > 0)
                            <h6 class="fw-bold text-danger mb-3"><i class="bi bi-exclamation-triangle me-2"></i>Failed Rows ({{ count($results['errors']) }})</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3">Row #</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>ID Number</th>
                                            <th>Role</th>
                                            <th>Errors</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($results['errors'] as $error)
                                            <tr>
                                                <td class="ps-3 fw-bold">{{ $error['row'] }}</td>
                                                <td>{{ $error['data']['name'] ?: '-' }}</td>
                                                <td>{{ $error['data']['email'] ?: '-' }}</td>
                                                <td>{{ $error['data']['id_number'] ?? '-' }}</td>
                                                <td>{{ $error['data']['role'] ?: '-' }}</td>
                                                <td class="text-danger small">
                                                    <ul class="mb-0 ps-3">
                                                        @foreach($error['messages'] as $msg)
                                                            <li>{{ $msg }}</li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <div class="d-flex gap-2 mt-4">
                            <a href="{{ route('admin.users.import') }}" class="btn btn-primary">
                                <i class="bi bi-upload me-1"></i> Import More
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-list me-1"></i> View All Users
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- Import Form -->
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-primary bg-gradient text-white py-3">
                                <h5 class="card-title fw-bold mb-0"><i class="bi bi-upload me-2"></i>Upload CSV File</h5>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    @if ($errors->any())
                                        <div class="alert alert-danger mb-4">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="mb-4">
                                        <label for="file" class="form-label fw-semibold">Select CSV File</label>
                                        <input type="file" class="form-control form-control-lg" id="file" name="file" accept=".csv" required>
                                        <div class="form-text">Upload a CSV file (max 5MB). Excel files should be saved as CSV first.</div>
                                    </div>

                                    <div class="d-grid gap-2 d-md-flex">
                                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                                            <i class="bi bi-cloud-upload me-2"></i>Import Users
                                        </button>
                                        <a href="{{ route('admin.users.import.template') }}" class="btn btn-outline-secondary btn-lg">
                                            <i class="bi bi-download me-2"></i>Download Template
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white py-3 border-bottom-0">
                                <h5 class="card-title fw-bold mb-0"><i class="bi bi-info-circle me-2"></i>CSV Format Guide</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small">Your CSV file must have the following columns:</p>
                                
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Column</th>
                                                <th>Required</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody class="small">
                                            <tr>
                                                <td><code>name</code></td>
                                                <td><span class="badge bg-danger">Yes</span></td>
                                                <td>Full name</td>
                                            </tr>
                                            <tr>
                                                <td><code>email</code></td>
                                                <td><span class="badge bg-danger">Yes</span></td>
                                                <td>Must be unique</td>
                                            </tr>
                                            <tr>
                                                <td><code>id_number</code></td>
                                                <td><span class="badge bg-danger">Yes</span></td>
                                                <td>Student ID</td>
                                            </tr>
                                            <tr>
                                                <td><code>role</code></td>
                                                <td><span class="badge bg-danger">Yes</span></td>
                                                <td>admin, mentor, or participant</td>
                                            </tr>
                                            <tr>
                                                <td><code>password</code></td>
                                                <td><span class="badge bg-danger">Yes</span></td>
                                                <td>Min 8 characters</td>
                                            </tr>
                                            <tr>
                                                <td><code>confirm_password</code></td>
                                                <td><span class="badge bg-danger">Yes</span></td>
                                                <td>Must match password</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="alert alert-info small mb-0">
                                    <i class="bi bi-lightbulb me-1"></i>
                                    <strong>Tip:</strong> Download the template file to see the expected format.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
