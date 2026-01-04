<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Welcome to {{ config('app.name', 'Laravel') }}</title>

        <!-- Bootstrap 5 CDN -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <style>
            body {
                font-family: 'Instrument Sans', sans-serif;
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            }
            .hero-section {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .card-hero {
                border: none;
                border-radius: 1.5rem;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(10px);
                transition: transform 0.3s ease;
            }
            .card-hero:hover {
                transform: translateY(-5px);
            }
            .btn-lg-custom {
                padding: 0.8rem 2rem;
                border-radius: 50rem;
                font-weight: 600;
                transition: all 0.3s ease;
            }
            .btn-primary-custom {
                background: linear-gradient(to right, #4e54c8, #8f94fb);
                border: none;
                color: white;
            }
            .btn-primary-custom:hover {
                background: linear-gradient(to right, #3a41ba, #7e84f9);
                transform: scale(1.05);
                color: white;
            }
            .btn-outline-custom {
                border: 2px solid #4e54c8;
                color: #4e54c8;
                background: transparent;
            }
            .btn-outline-custom:hover {
                background: #4e54c8;
                color: white;
                transform: scale(1.05);
            }
            .feature-icon {
                font-size: 2.5rem;
                margin-bottom: 1rem;
                background: -webkit-linear-gradient(#4e54c8, #8f94fb);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
        </style>
    </head>
    <body>
        <div class="hero-section text-center p-3">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card card-hero p-5">
                            <div class="card-body">
                                <div class="mb-4">
                                     <i class="bi bi-mortarboard-fill display-1 bg-gradient-primary text-primary" style="background: -webkit-linear-gradient(#4e54c8, #8f94fb);-webkit-background-clip: text;-webkit-text-fill-color: transparent;"></i>
                                </div>
                                <h1 class="display-4 fw-bold mb-3 text-dark">Welcome to {{ config('app.name', 'TugasWeb') }}</h1>
                                <p class="lead text-muted mb-5">
                                    Your modern platform for seamless class management, attendance tracking, and task submission. 
                                    Empowering mentors and participants alike.
                                </p>

                                @if (Route::has('login'))
                                    <div class="d-flex justify-content-center gap-3">
                                        @auth
                                            <a href="{{ url('/dashboard') }}" class="btn btn-primary-custom btn-lg-custom shadow">
                                                Go to Dashboard <i class="bi bi-arrow-right ms-2"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-primary-custom btn-lg-custom shadow">
                                                Log in
                                            </a>

                                            @if (Route::has('register'))
                                                <a href="{{ route('register') }}" class="btn btn-outline-custom btn-lg-custom">
                                                    Register
                                                </a>
                                            @endif
                                        @endauth
                                    </div>
                                @endif

                                <div class="row mt-5 pt-4 g-4 border-top">
                                    <div class="col-md-4">
                                        <div class="feature-icon"><i class="bi bi-people"></i></div>
                                        <h5 class="fw-bold">Class Management</h5>
                                        <p class="text-muted small">Organize classes and participants efficiently.</p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="feature-icon"><i class="bi bi-check-circle"></i></div>
                                        <h5 class="fw-bold">Attendance</h5>
                                        <p class="text-muted small">Real-time tracking and reporting.</p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="feature-icon"><i class="bi bi-file-earmark-text"></i></div>
                                        <h5 class="fw-bold">Tasks</h5>
                                        <p class="text-muted small">Seamless submission and grading workflow.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-muted small">
                            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
