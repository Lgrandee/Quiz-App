@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-tachometer-alt"></i> Welkom, {{ auth()->user()->name }}!</h2>
                <span class="badge bg-primary fs-6">Student Dashboard</span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Available Quizzes -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5><i class="fas fa-list-alt"></i> Beschikbare Quizzes</h5>
                </div>
                <div class="card-body">
                    @if($quizzes->count() > 0)
                        <div class="row">
                            @foreach($quizzes as $quiz)
                                @php
                                    $sessionKey = 'quiz_answers_' . $quiz->id;
                                    $savedAnswers = session($sessionKey, []);
                                    $totalQuestions = $quiz->questions->count();
                                    $answeredQuestions = count($savedAnswers);
                                    $progressPercentage = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0;
                                    $isInProgress = $answeredQuestions > 0 && $answeredQuestions < $totalQuestions;
                                    $isCompleted = $answeredQuestions >= $totalQuestions;
                                    $status = [
                                        'color' => $isInProgress ? 'warning' : ($isCompleted ? 'success' : 'primary'),
                                        'text' => $isInProgress ? 'In Uitvoering' : ($isCompleted ? 'Voltooid' : 'Nieuw')
                                    ];
                                @endphp
                                
                                <div class="col-md-6 mb-4">
                                    <div class="card quiz-card h-100 {{ $isInProgress ? 'border-warning' : ($isCompleted ? 'border-success' : 'border-primary') }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h5 class="card-title mb-0">{{ $quiz->title }}</h5>
                                                <div>
                                                    @if($quiz->quiz_type === 'open_question')
                                                        <span class="badge bg-success me-1">
                                                            <i class="fas fa-edit"></i> Open Vragen
                                                        </span>
                                                    @elseif($quiz->quiz_type === 'multiple_choice')
                                                        <span class="badge bg-primary me-1">
                                                            <i class="fas fa-check-circle"></i> Multiple Choice
                                                        </span>
                                                    @else
                                                        <span class="badge bg-info me-1">
                                                            <i class="fas fa-list"></i> Gemixed
                                                        </span>
                                                    @endif
                                                    <span class="badge bg-{{ $status['color'] }}">{{ $status['text'] }}</span>
                                                </div>
                                            </div>
                                            
                                            <p class="card-text text-muted small">{{ $quiz->description }}</p>
                                            
                                            <div class="quiz-stats mb-3">
                                                <div class="row text-center">
                                                    <div class="col-4">
                                                        <div class="stat-item">
                                                            <i class="fas fa-question-circle text-primary"></i>
                                                            <small class="d-block">{{ $totalQuestions }} vragen</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="stat-item">
                                                            <i class="fas fa-clock text-info"></i>
                                                            <small class="d-block">{{ $quiz->time_limit ?? 'Geen' }} min</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="stat-item">
                                                            <i class="fas fa-star text-warning"></i>
                                                            <small class="d-block">{{ $quiz->total_points }} punten</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($totalQuestions > 0)
                                                <div class="progress mb-3" style="height: 8px;">
                                                    <div class="progress-bar bg-{{ $isCompleted ? 'success' : ($isInProgress ? 'warning' : 'primary') }}" 
                                                         style="width: {{ $progressPercentage }}%"></div>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $answeredQuestions }} van {{ $totalQuestions }} vragen beantwoord ({{ $progressPercentage }}%)
                                                </small>
                                            @endif
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            @if($isInProgress)
                                                <a href="{{ route('student.quiz.show', $quiz->id) }}" class="btn btn-warning btn-sm w-100">
                                                    <i class="fas fa-play"></i> Quiz Hervatten
                                                </a>
                                            @elseif($isCompleted)
                                                <div class="d-grid gap-2">
                                                    <a href="{{ route('student.quiz.show', $quiz->id) }}" class="btn btn-success btn-sm">
                                                        <i class="fas fa-redo"></i> Opnieuw Maken
                                                    </a>
                                                </div>
                                            @else
                                                <a href="{{ route('student.quiz.show', $quiz->id) }}" class="btn btn-primary btn-sm w-100">
                                                    <i class="fas fa-play"></i> Quiz Starten
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Geen Quizzes Beschikbaar</h5>
                            <p class="text-muted">Er zijn momenteel geen actieve quizzes om te maken.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6><i class="fas fa-chart-pie"></i> Jouw Statistieken</h6>
                </div>
                <div class="card-body">
                    <div class="stat-row mb-3">
                        <div class="d-flex justify-content-between">
                            <span><i class="fas fa-clipboard-check text-success"></i> Voltooide Quizzes</span>
                            <strong>{{ $recentAttempts->where('completed_at', '!=', null)->count() }}</strong>
                        </div>
                    </div>
                    <div class="stat-row mb-3">
                        <div class="d-flex justify-content-between">
                            <span><i class="fas fa-clock text-warning"></i> In Uitvoering</span>
                            <strong>
                                @php
                                    $inProgressCount = 0;
                                    foreach($quizzes as $quiz) {
                                        $savedAnswers = session('quiz_answers_' . $quiz->id, []);
                                        $totalQuestions = $quiz->questions->count();
                                        if(count($savedAnswers) > 0 && count($savedAnswers) < $totalQuestions) {
                                            $inProgressCount++;
                                        }
                                    }
                                @endphp
                                {{ $inProgressCount }}
                            </strong>
                        </div>
                    </div>
                    <div class="stat-row">
                        <div class="d-flex justify-content-between">
                            <span><i class="fas fa-list text-primary"></i> Totaal Beschikbaar</span>
                            <strong>{{ $quizzes->count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6><i class="fas fa-history"></i> Recente Activiteit</h6>
                </div>
                <div class="card-body">
                    @if($recentAttempts->count() > 0)
                        @foreach($recentAttempts->take(3) as $attempt)
                            <div class="activity-item mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $attempt->quiz->title }}</h6>
                                        <small class="text-muted">
                                            @if($attempt->completed_at)
                                                <i class="fas fa-check-circle text-success"></i> 
                                                Voltooid op {{ $attempt->completed_at->format('d/m/Y H:i') }}
                                            @else
                                                <i class="fas fa-clock text-warning"></i> 
                                                Gestart op {{ $attempt->created_at->format('d/m/Y H:i') }}
                                            @endif
                                        </small>
                                    </div>
                                    @if($attempt->completed_at && $attempt->score !== null)
                                        <span class="badge bg-{{ $attempt->score >= 80 ? 'success' : ($attempt->score >= 60 ? 'warning' : 'danger') }}">
                                            {{ $attempt->score }}%
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        <div class="text-center">
                            <a href="{{ route('student.quiz-attempts.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-list"></i> Alle Pogingen Bekijken
                            </a>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-history fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Nog geen quiz activiteit</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.quiz-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.quiz-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.stat-item {
    padding: 0.5rem 0;
}

.stat-row {
    padding: 0.5rem 0;
}

.activity-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}
</style>
@endsection
