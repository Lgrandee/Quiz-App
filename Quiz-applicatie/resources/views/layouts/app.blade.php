<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'Moderne quiz applicatie voor docenten en studenten')">
    <title>@yield('title', 'Quiz Application')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --bg-light: #f9fafb;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --border-radius: 0.75rem;
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: var(--text-primary);
            background-color: var(--bg-light);
        }

        /* Accessibility improvements */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* Focus styles for accessibility */
        .btn:focus,
        .form-control:focus,
        .form-select:focus,
        .nav-link:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
            box-shadow: none;
        }

        /* Skip to main content link */
        .skip-link {
            position: absolute;
            top: -40px;
            left: 6px;
            background: var(--primary-color);
            color: white;
            padding: 8px;
            text-decoration: none;
            border-radius: 4px;
            z-index: 1000;
        }

        .skip-link:focus {
            top: 6px;
        }

        /* Modern navigation */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%) !important;
            box-shadow: var(--shadow-md);
            border: none;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
        }

        .nav-link {
            font-weight: 500;
            transition: var(--transition);
            border-radius: 0.5rem;
            margin: 0 0.25rem;
            padding: 0.5rem 1rem !important;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }

        /* Cards and components */
        .card {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }

        .quiz-card {
            transition: var(--transition);
            cursor: pointer;
        }

        .quiz-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        /* Buttons */
        .btn {
            font-weight: 500;
            border-radius: 0.5rem;
            transition: var(--transition);
            border: none;
            padding: 0.75rem 1.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        /* Loading states */
        .loading {
            position: relative;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Progress indicators */
        .progress-bar {
            background-color: var(--primary-color);
            transition: width 0.3s ease;
        }

        /* Enhanced Mobile responsiveness */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.1rem;
            }
            
            .card {
                margin-bottom: 1rem;
            }
            
            .btn {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }
            
            .btn-group {
                flex-direction: column;
                width: 100%;
            }
            
            .btn-group .btn {
                margin-bottom: 0.5rem;
                border-radius: var(--border-radius) !important;
            }
            
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .modal-dialog {
                margin: 0.5rem;
            }
            
            .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .d-flex.justify-content-between {
                flex-direction: column;
                align-items: stretch !important;
            }
            
            .d-flex.justify-content-between > * {
                margin-bottom: 1rem;
            }
            
            .d-flex.justify-content-between > *:last-child {
                margin-bottom: 0;
            }
        }
        
        @media (max-width: 576px) {
            .col-md-3, .col-md-4, .col-md-6, .col-md-8 {
                margin-bottom: 1rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .btn-sm {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
            
            .navbar-nav {
                text-align: center;
            }
            
            .navbar-nav .nav-link {
                padding: 0.75rem 1rem;
            }
            
            .quiz-card .card-body {
                padding: 0.75rem;
            }
            
            .progress {
                height: 0.75rem;
            }
        }
        
        /* Touch-friendly improvements */
        @media (hover: none) and (pointer: coarse) {
            .btn, .nav-link, .dropdown-item {
                min-height: 44px;
                display: flex;
                align-items: center;
            }
            
            .form-control, .form-select {
                min-height: 44px;
            }
            
            .card {
                cursor: pointer;
            }
            
            .quiz-card:hover {
                transform: none;
                box-shadow: var(--shadow-md);
            }
        }

        /* High contrast mode support */
        @media (prefers-contrast: high) {
            :root {
                --primary-color: #000080;
                --text-primary: #000000;
                --border-color: #000000;
            }
        }

        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            :root {
                --text-primary: #f9fafb;
                --text-secondary: #d1d5db;
                --bg-light: #111827;
                --border-color: #374151;
            }
            
            body {
                background-color: var(--bg-light);
                color: var(--text-primary);
            }
            
            .card {
                background-color: #1f2937;
                border-color: var(--border-color);
            }
        }

        /* Help tooltip styles */
        .help-tooltip {
            position: relative;
            display: inline-block;
            cursor: help;
        }

        .help-tooltip .tooltip-text {
            visibility: hidden;
            width: 200px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 8px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.875rem;
        }

        .help-tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        /* Alert improvements */
        .alert {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--shadow-sm);
        }

        /* Form improvements */
        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="skip-link">Spring naar hoofdinhoud</a>
    
    <!-- Loading overlay -->
    <div id="loading-overlay" class="d-none position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50" style="z-index: 9999;">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="spinner-border text-primary" role="status" aria-label="Laden...">
                <span class="sr-only">Laden...</span>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow" role="navigation" aria-label="Hoofdnavigatie">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}" aria-label="Quiz App - Naar startpagina">
                <i class="fas fa-graduation-cap me-2" aria-hidden="true"></i>Quiz App
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
                                <i class="fas fa-tachometer-alt me-1" aria-hidden="true"></i>Dashboard
                            </a>
                            <a class="nav-link" href="{{ route('teacher.questions.index') }}" aria-label="Vragen beheren">
                                <i class="fas fa-question-circle me-1" aria-hidden="true"></i>Vragen
                            </a>
                            <a class="nav-link" href="{{ route('teacher.quizzes.index') }}" aria-label="Quizzes beheren">
                                <i class="fas fa-list me-1" aria-hidden="true"></i>Quizzes
                            </a>
                        @else
                            <a class="nav-link" href="{{ route('student.dashboard') }}" aria-label="Student dashboard">
                                <i class="fas fa-tachometer-alt me-1" aria-hidden="true"></i>Dashboard
                            </a>
                            <a class="nav-link" href="{{ route('student.progress') }}" aria-label="Voortgang bekijken">
                                <i class="fas fa-chart-line me-1" aria-hidden="true"></i>Voortgang
                            </a>
                        @endif
                        
                        <!-- Help button -->
                        <button class="nav-link btn btn-link" type="button" data-bs-toggle="modal" data-bs-target="#helpModal" aria-label="Help en documentatie">
                            <i class="fas fa-question-circle me-1" aria-hidden="true"></i>Help
                        </button>
                        
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" 
                               aria-expanded="false" aria-label="Gebruikersmenu voor {{ auth()->user()->name }}">
                                <i class="fas fa-user me-1" aria-hidden="true"></i>{{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('logout') }}" 
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                       aria-label="Uitloggen uit de applicatie">
                                    <i class="fas fa-sign-out-alt me-1" aria-hidden="true"></i>Uitloggen
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
                            <i class="fas fa-sign-in-alt me-1" aria-hidden="true"></i>Inloggen
                        </a>
                        <a class="nav-link" href="{{ route('register') }}" aria-label="Nieuw account aanmaken">
                            <i class="fas fa-user-plus me-1" aria-hidden="true"></i>Registreren
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="py-4" id="main-content" tabindex="-1">
        @yield('content')
    </main>

    <!-- Help Modal -->
    <div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="helpModalLabel">
                        <i class="fas fa-question-circle me-2"></i>Help & Documentatie
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluit help venster"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-chalkboard-teacher me-2"></i>Voor Docenten</h6>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-decoration-none" onclick="showHelp('upload-questions')">
                                    <i class="fas fa-upload me-1"></i>Vragen uploaden
                                </a></li>
                                <li><a href="#" class="text-decoration-none" onclick="showHelp('create-quiz')">
                                    <i class="fas fa-plus me-1"></i>Quiz maken
                                </a></li>
                                <li><a href="#" class="text-decoration-none" onclick="showHelp('view-results')">
                                    <i class="fas fa-chart-bar me-1"></i>Resultaten bekijken
                                </a></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-user-graduate me-2"></i>Voor Studenten</h6>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-decoration-none" onclick="showHelp('take-quiz')">
                                    <i class="fas fa-play me-1"></i>Quiz maken
                                </a></li>
                                <li><a href="#" class="text-decoration-none" onclick="showHelp('view-progress')">
                                    <i class="fas fa-chart-line me-1"></i>Voortgang bekijken
                                </a></li>
                                <li><a href="#" class="text-decoration-none" onclick="showHelp('open-questions')">
                                    <i class="fas fa-edit me-1"></i>Open vragen
                                </a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div id="help-content" class="mt-4 p-3 bg-light rounded">
                        <p class="text-muted">Selecteer een onderwerp hierboven voor gedetailleerde uitleg.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sluiten</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast container for notifications -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Global JavaScript for UI/UX improvements -->
    <script>
        // Loading state management
        function showLoading() {
            document.getElementById('loading-overlay').classList.remove('d-none');
        }
        
        function hideLoading() {
            document.getElementById('loading-overlay').classList.add('d-none');
        }
        
        // Auto-hide loading on page load
        document.addEventListener('DOMContentLoaded', function() {
            hideLoading();
        });
        
        // Show loading on form submissions
        document.addEventListener('submit', function(e) {
            if (!e.target.classList.contains('no-loading')) {
                showLoading();
            }
        });
        
        // Show loading on navigation
        document.addEventListener('click', function(e) {
            if (e.target.matches('a[href]:not([href^="#"]):not([href^="javascript:"]):not(.no-loading)')) {
                showLoading();
            }
        });
        
        // Toast notification system
        function showToast(message, type = 'info') {
            const toastId = 'toast-' + Date.now();
            const toastHtml = `
                <div class="toast" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <i class="fas fa-${type === 'success' ? 'check-circle text-success' : type === 'error' ? 'exclamation-circle text-danger' : 'info-circle text-primary'} me-2"></i>
                        <strong class="me-auto">Melding</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Sluit melding"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;
            
            document.getElementById('toast-container').insertAdjacentHTML('beforeend', toastHtml);
            const toast = new bootstrap.Toast(document.getElementById(toastId));
            toast.show();
            
            // Auto-remove after hiding
            document.getElementById(toastId).addEventListener('hidden.bs.toast', function() {
                this.remove();
            });
        }
        
        // Help system
        const helpContent = {
            'upload-questions': {
                title: 'Vragen Uploaden',
                content: `
                    <h6>CSV Formaat</h6>
                    <p>Upload vragen via CSV met de volgende kolommen:</p>
                    <ul>
                        <li><strong>Multiple Choice:</strong> vraag_id;type;vraag;antwoord_a;antwoord_b;antwoord_c;juiste_antwoord</li>
                        <li><strong>Open Vragen:</strong> vraag_id;type;vraag;juiste_antwoord</li>
                    </ul>
                    <p><strong>Tip:</strong> Download de template voor het juiste formaat.</p>
                `
            },
            'create-quiz': {
                title: 'Quiz Maken',
                content: `
                    <h6>Stappen:</h6>
                    <ol>
                        <li>Ga naar "Quizzes" → "Nieuwe Quiz"</li>
                        <li>Vul titel en beschrijving in</li>
                        <li>Selecteer vragen uit de lijst</li>
                        <li>Stel optioneel een tijdslimiet in</li>
                        <li>Activeer de quiz voor studenten</li>
                    </ol>
                `
            },
            'take-quiz': {
                title: 'Quiz Maken',
                content: `
                    <h6>Hoe een quiz maken:</h6>
                    <ul>
                        <li>Klik op een quiz in je dashboard</li>
                        <li>Beantwoord vragen één voor één</li>
                        <li>Je antwoorden worden automatisch opgeslagen</li>
                        <li>Klik "Quiz Voltooien" als je klaar bent</li>
                    </ul>
                    <p><strong>Open vragen:</strong> Antwoorden zijn niet hoofdlettergevoelig.</p>
                `
            },
            'open-questions': {
                title: 'Open Vragen',
                content: `
                    <h6>Tips voor open vragen:</h6>
                    <ul>
                        <li>Hoofdletters maken niet uit</li>
                        <li>Extra spaties worden genegeerd</li>
                        <li>Je krijgt direct feedback tijdens het typen</li>
                        <li>Similarity scoring toont hoe dicht je bij het juiste antwoord zit</li>
                    </ul>
                `
            }
        };
        
        function showHelp(topic) {
            const content = helpContent[topic];
            if (content) {
                document.getElementById('help-content').innerHTML = `
                    <h6>${content.title}</h6>
                    ${content.content}
                `;
            }
        }
        
        // Keyboard navigation improvements
        document.addEventListener('keydown', function(e) {
            // ESC key closes modals
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal.show');
                if (openModal) {
                    bootstrap.Modal.getInstance(openModal).hide();
                }
            }
        });
        
        // Auto-focus first form input
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('form input:not([type="hidden"]), form select, form textarea');
            if (firstInput && !firstInput.hasAttribute('autofocus')) {
                firstInput.focus();
            }
        });
        
        // Confirm dangerous actions
        document.addEventListener('click', function(e) {
            if (e.target.matches('.btn-danger, .text-danger') && 
                (e.target.textContent.includes('Verwijder') || e.target.textContent.includes('Delete'))) {
                if (!confirm('Weet je zeker dat je deze actie wilt uitvoeren?')) {
                    e.preventDefault();
                }
            }
        });
        
        // Progress indication for long forms
        function updateProgress() {
            const forms = document.querySelectorAll('form[data-progress]');
            forms.forEach(form => {
                const inputs = form.querySelectorAll('input:required, select:required, textarea:required');
                const filled = Array.from(inputs).filter(input => input.value.trim() !== '').length;
                const progress = (filled / inputs.length) * 100;
                
                let progressBar = form.querySelector('.form-progress');
                if (!progressBar) {
                    progressBar = document.createElement('div');
                    progressBar.className = 'progress mb-3 form-progress';
                    progressBar.innerHTML = '<div class="progress-bar" role="progressbar"></div>';
                    form.insertBefore(progressBar, form.firstChild);
                }
                
                const bar = progressBar.querySelector('.progress-bar');
                bar.style.width = progress + '%';
                bar.setAttribute('aria-valuenow', progress);
            });
        }
        
        // Update progress on input changes
        document.addEventListener('input', updateProgress);
        document.addEventListener('change', updateProgress);
    </script>
    
    @yield('scripts')
</body>
</html>