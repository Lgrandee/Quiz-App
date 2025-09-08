<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quiz Application')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .quiz-card {
            transition: transform 0.2s;
        }
        .quiz-card:hover {
            transform: translateY(-5px);
        }
        .score-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-graduation-cap me-2"></i>Quiz App
            </a>
            
            @auth
                <div class="navbar-nav ms-auto">
                    @if(auth()->user()->isTeacher())
                        <a class="nav-link" href="{{ route('quizzes.index') }}">
                            <i class="fas fa-list me-1"></i>My Quizzes
                        </a>
                        <a class="nav-link" href="{{ route('questions.index') }}">
                            <i class="fas fa-question-circle me-1"></i>Questions
                        </a>
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-upload me-1"></i>Upload CSV
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('questions.create') }}">
                                    <i class="fas fa-check-circle text-primary me-1"></i>Multiple Choice
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('questions.create-open') }}">
                                    <i class="fas fa-edit text-success me-1"></i>Open Vragen
                                </a></li>
                            </ul>
                        </div>
                        <a class="nav-link" href="{{ route('quizzes.create') }}">
                            <i class="fas fa-plus me-1"></i>Create Quiz
                        </a>
                        <a class="nav-link" href="{{ route('quiz-attempts.index') }}">
                            <i class="fas fa-chart-bar me-1"></i>Results
                        </a>
                    @else
                        <a class="nav-link" href="{{ route('student.dashboard') }}">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                        <a class="nav-link" href="{{ route('quiz.show') }}">
                            <i class="fas fa-play me-1"></i>Take Quiz
                        </a>
                        <a class="nav-link" href="{{ route('quiz-attempts.index') }}">
                            <i class="fas fa-history me-1"></i>My Attempts
                        </a>
                    @endif
                    
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>{{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><span class="dropdown-item-text">{{ ucfirst(auth()->user()->role) }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            @else
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                </div>
            @endauth
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>