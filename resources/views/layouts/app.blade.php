<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <script>
            // Initialize theme immediately to prevent flash
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);

            window.toggleTheme = function() {
                const currentTheme = document.documentElement.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                
                // Update icon if it exists
                const icon = document.getElementById('theme-icon');
                if (icon) {
                    icon.className = newTheme === 'dark' ? 'bi bi-moon-stars me-2' : 'bi bi-sun me-2';
                }
                
                // Update text if it exists
                const text = document.getElementById('theme-text');
                if (text) {
                    text.textContent = newTheme === 'dark' ? 'Dark Mode' : 'Light Mode';
                }
            }
        </script>

        <style>
                font-family: 'Inter', sans-serif;
                background-color: var(--bs-body-bg);
                color: var(--bs-body-color);
            }
            .navbar-brand {
                font-weight: 700;
                color: #4f46e5 !important;
            }
            [data-bs-theme="dark"] .navbar-brand {
                color: #818cf8 !important;
            }
            [data-bs-theme="dark"] .card {
                background-color: #1f2937;
                border-color: #374151;
            }
            [data-bs-theme="dark"] .navbar {
                background-color: #1f2937 !important;
                border-color: #374151 !important;
            }
            [data-bs-theme="dark"] body {
                background-color: #111827;
            }
            .navbar-brand {
                font-weight: 700;
                color: #4f46e5 !important;
            }
            .card {
                border: none;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                border-radius: 0.75rem;
            }
            .btn-primary {
                background-color: #4f46e5;
                border-color: #4f46e5;
            }
            .btn-primary:hover {
                background-color: #4338ca;
                border-color: #4338ca;
            }
            .nav-link.active {
                color: #4f46e5 !important;
                font-weight: 600;
            }
        </style>
    </head>
    <body>
        <div class="min-vh-100 d-flex flex-column">
            @include('layouts.navigation')

            <!-- Page Content -->
            <main class="flex-grow-1 py-4">
                <div class="container">
                    @if(isset($header))
                        <header class="mb-4 pb-3 border-bottom">
                            <h2 class="h3 fw-bold text-dark m-0">
                                {{ $header }}
                            </h2>
                        </header>
                    @endif

                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>

            <footer class="bg-white border-top py-3 mt-auto">
                <div class="container text-center text-muted small">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
                </div>
            </footer>
        </div>

        <!-- Bootstrap 5 JS Bundle -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
