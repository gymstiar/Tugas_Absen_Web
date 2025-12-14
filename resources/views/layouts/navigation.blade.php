<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <i class="bi bi-mortarboard-fill text-primary fs-4 me-2"></i>
            <span>Class Mentor</span>
        </a>

        <!-- Hamburger Menu -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <!-- Left Side Links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                </li>

                <!-- Admin Links -->
                @if(Auth::user()->role === 'admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}" href="{{ route('admin.classes.index') }}">
                            <i class="bi bi-people me-1"></i> Classes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <i class="bi bi-person-badge me-1"></i> Users
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-graph-up me-1"></i> Reports
                        </a>
                        <ul class="dropdown-menu border-0 shadow-sm rounded-3">
                            <li><a class="dropdown-item" href="{{ route('admin.reports.index') }}">Overview</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('admin.reports.attendance') }}">Attendance Report</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.reports.tasks') }}">Task Report</a></li>
                        </ul>
                    </li>
                @endif

                <!-- Mentor Links -->
                @if(Auth::user()->role === 'mentor')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mentor.attendance.*') ? 'active' : '' }}" href="{{ route('mentor.attendance.index') }}">
                            <i class="bi bi-calendar-check me-1"></i> Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mentor.tasks.*') ? 'active' : '' }}" href="{{ route('mentor.tasks.index') }}">
                            <i class="bi bi-clipboard-check me-1"></i> Tasks
                        </a>
                    </li>
                @endif

                <!-- Participant Links -->
                @if(Auth::user()->role === 'participant')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('participant.attendance.*') ? 'active' : '' }}" href="{{ route('participant.attendance.index') }}">
                            <i class="bi bi-calendar-check me-1"></i> My Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('participant.tasks.*') ? 'active' : '' }}" href="{{ route('participant.tasks.index') }}">
                            <i class="bi bi-clipboard-data me-1"></i> My Tasks
                        </a>
                    </li>
                @endif
            </ul>

            <!-- Right Side (User Profile) -->
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 14px;">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="fw-medium">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                        <li><h6 class="dropdown-header text-uppercase small text-muted">{{ ucfirst(Auth::user()->role) }}</h6></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-2"></i> Profile
                            </a>
                        </li>
                        <li>
                            <button class="dropdown-item d-flex align-items-center" onclick="toggleTheme()">
                                <i class="bi bi-sun me-2" id="theme-icon"></i> 
                                <span id="theme-text">Light Mode</span>
                            </button>
                            <script>
                                // Set initial icon and text based on current theme
                                (function() {
                                    const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
                                    const icon = document.getElementById('theme-icon');
                                    const text = document.getElementById('theme-text');
                                    if (icon && text) {
                                        icon.className = currentTheme === 'dark' ? 'bi bi-moon-stars me-2' : 'bi bi-sun me-2';
                                        text.textContent = currentTheme === 'dark' ? 'Dark Mode' : 'Light Mode';
                                    }
                                })();
                            </script>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Log Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
