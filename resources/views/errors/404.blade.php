<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - 404</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }
        .error-container {
            text-align: center;
            max-width: 600px;
            padding: 40px;
            position: relative;
            z-index: 10;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            color: #dc3545; /* Bootstrap danger color */
            text-shadow: 4px 4px 0px rgba(0,0,0,0.1);
            line-height: 1;
        }
        .error-icon {
            font-size: 5rem;
            color: #6c757d;
        }
        .btn-home {
            transition: all 0.3s ease;
        }
        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .animate-bounce {
            animation: bounce 2s infinite ease-in-out;
            display: inline-block;
        }
        /* Background circles decoration */
        .bg-circle {
            position: absolute;
            border-radius: 50%;
            z-index: -1;
            opacity: 0.1;
        }
        .bg-circle-1 {
            width: 300px;
            height: 300px;
            background-color: #0d6efd;
            top: -100px;
            left: -100px;
        }
        .bg-circle-2 {
            width: 200px;
            height: 200px;
            background-color: #dc3545;
            bottom: -50px;
            right: -50px;
        }
    </style>
</head>
<body>
    <div class="bg-circle bg-circle-1"></div>
    <div class="bg-circle bg-circle-2"></div>

    <div class="container error-container">
        <div class="mb-4">
            <i class="bi bi-robot error-icon animate-bounce text-dark"></i>
        </div>
        <h1 class="error-code mb-3">404</h1>
        <h2 class="fw-bold mb-3 text-dark">Houston, we have a problem!</h2>
        <p class="lead text-muted mb-5">
            The page you are looking for seems to have gone on a vacation without leaving a forwarding address. 
            It might have been moved, deleted, or possibly abducted by aliens. 👽
        </p>
        
        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5 py-3 rounded-pill fw-bold shadow-sm btn-home">
                <i class="bi bi-house-door-fill me-2"></i> Take Me Home
            </a>
            <button onclick="history.back()" class="btn btn-outline-secondary btn-lg px-4 py-3 rounded-pill fw-semibold ms-sm-3 mt-3 mt-sm-0">
                <i class="bi bi-arrow-left me-2"></i> Go Back
            </button>
        </div>
    </div>
</body>
</html>
