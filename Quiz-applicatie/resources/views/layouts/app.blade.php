<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'Moderne quiz applicatie voor docenten en studenten')">
    <title>@yield('title', 'Quiz Application')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary" role="navigation" aria-label="Hoofdnavigatie">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}" aria-label="Quiz App - Naar startpagina">
                Quiz App
            </a>
            
            <!-- Mobile menu toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Navigatie menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                @auth
                    <div class="navbar-nav ms-auto">
                        @if(auth()->user()->isTeacher())
                            <a class="nav-link" href="{{ route('teacher.dashboard') }}" aria-label="Docenten dashboard">
                                Dashboard
                            </a>
                            <a class="nav-link" href="{{ route('teacher.questions.index') }}" aria-label="Vragen beheren">
                                Vragen
                            </a>
                            <a class="nav-link" href="{{ route('teacher.quizzes.index') }}" aria-label="Quizzes beheren">
                                Quizzes
                            </a>
                        @else
                            <a class="nav-link" href="{{ route('student.dashboard') }}" aria-label="Student dashboard">
                                Dashboard
                            </a>
                            <a class="nav-link" href="{{ route('student.progress') }}" aria-label="Voortgang bekijken">
                                Voortgang
                            </a>
                        @endif

                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                               aria-expanded="false" aria-label="Gebruikersmenu voor {{ auth()->user()->name }}">
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                       aria-label="Uitloggen uit de applicatie">
                                    Uitloggen
                                </a></li>
                            </ul>
                        </div>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                @else
                    <div class="navbar-nav ms-auto">
                        <a class="nav-link" href="{{ route('login') }}" aria-label="Inloggen in de applicatie">
                            Inloggen
                        </a>
                        <a class="nav-link" href="{{ route('register') }}" aria-label="Nieuw account aanmaken">
                            Registreren
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="py-4" id="main-content" tabindex="-1">
        @yield('content')
    </main>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html>