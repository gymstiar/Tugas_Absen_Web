<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <script>
            // Initialize theme immediately
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        </script>

        <style>
            [data-bs-theme="dark"] body {
                background-color: #111827 !important;
                color: #f3f4f6;
            }
            [data-bs-theme="dark"] .bg-light {
                background-color: #111827 !important;
            }
            [data-bs-theme="dark"] .auth-card {
                background-color: #1f2937 !important;
                border-color: #374151;
            }
            [data-bs-theme="dark"] .text-dark {
                color: #f3f4f6 !important;
            }
            [data-bs-theme="dark"] .text-muted {
                color: #9ca3af !important;
            }
            
            body {
                font-family: 'Inter', sans-serif;
                background-color: #f8f9fa;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
            }
            .auth-card {
                width: 100%;
                max-width: 420px;
                border: none;
                border-radius: 1rem;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }
            .brand-logo {
                font-size: 2.5rem;
                color: #4f46e5;
            }
            .btn-primary {
                background-color: #4f46e5;
                border-color: #4f46e5;
                padding: 0.75rem;
                font-weight: 600;
            }
            .btn-primary:hover {
                background-color: #4338ca;
                border-color: #4338ca;
            }
            .form-floating > .form-control:focus ~ label,
            .form-floating > .form-control:not(:placeholder-shown) ~ label {
                color: #4f46e5;
                transform: scale(.85) translateY(-0.5rem) translateX(0.15rem);
            }
            .form-control:focus {
                border-color: #4f46e5;
                box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
            }
        </style>
    </head>
    <body class="bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="text-center mb-4">
                        <a href="/" class="text-decoration-none">
                            <i class="bi bi-mortarboard-fill brand-logo"></i>
                            <h2 class="h4 mt-2 mb-0 fw-bold text-dark">Class Mentor</h2>
                        </a>
                    </div>

                    <div class="card auth-card bg-white p-4 p-md-5">
                        {{ $slot }}
                    </div>
                    
                    <div class="text-center mt-4 text-muted small">
                        &copy; {{ date('Y') }} Class Mentor System
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
