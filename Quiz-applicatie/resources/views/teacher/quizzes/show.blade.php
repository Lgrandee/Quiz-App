@extends('layouts.app')

@section('title', 'Quiz Details')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-eye me-2"></i>Quiz Details</h2>
        <div class="btn-group">
            <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i>Bewerken
            </a>
            <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Terug
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Quiz Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Quiz Informatie</h5>
                    @if($quiz->is_active)
                        <span class="badge bg-success">Actief</span>
                    @else
                        <span class="badge bg-secondary">Inactief</span>
                    @endif
                </div>
                <div class="card-body">
                    <h4>{{ $quiz->title }}</h4>
                    
                    @if($quiz->description)
                        <p class="text-muted">{{ $quiz->description }}</p>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <strong>Aantal Vragen:</strong> {{ $quiz->questions->count() }}<br>
                            <strong>Aangemaakt:</strong> {{ $quiz->created_at->format('d/m/Y H:i') }}<br>
                            @if($quiz->updated_at != $quiz->created_at)
                                <strong>Laatst Bijgewerkt:</strong> {{ $quiz->updated_at->format('d/m/Y H:i') }}<br>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($quiz->time_limit)
                                <strong>Tijdslimiet:</strong> {{ $quiz->time_limit }} minuten<br>
                            @else
                                <strong>Tijdslimiet:</strong> Geen limiet<br>
                            @endif
                            <strong>Totaal Pogingen:</strong> {{ $quiz->attempts->count() }}<br>
                            <strong>Voltooide Pogingen:</strong> {{ $quiz->attempts->whereNotNull('completed_at')->count() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz Questions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Vragen ({{ $quiz->questions->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($quiz->questions->count() > 0)
                        @foreach($quiz->questions as $index => $question)
                            <div class="question-item mb-4 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">
                                        <span class="badge bg-secondary me-2">{{ $index + 1 }}</span>
                                        {{ $question->question_text }}
                                    </h6>
                                    <div>
                                        @if($question->type === 'multiple_choice')
                                            <span class="badge bg-primary">Multiple Choice</span>
                                        @else
                                            <span class="badge bg-info">Open Vraag</span>
                                        @endif
                                    </div>
                                </div>

                                @if($question->type === 'multiple_choice')
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" disabled 
                                                       {{ $question->correct_answer === 'a' ? 'checked' : '' }}>
                                                <label class="form-check-label {{ $question->correct_answer === 'a' ? 'text-success fw-bold' : '' }}">
                                                    A: {{ $question->answer_a }}
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" disabled 
                                                       {{ $question->correct_answer === 'b' ? 'checked' : '' }}>
                                                <label class="form-check-label {{ $question->correct_answer === 'b' ? 'text-success fw-bold' : '' }}">
                                                    B: {{ $question->answer_b }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" disabled 
                                                       {{ $question->correct_answer === 'c' ? 'checked' : '' }}>
                                                <label class="form-check-label {{ $question->correct_answer === 'c' ? 'text-success fw-bold' : '' }}">
                                                    C: {{ $question->answer_c }}
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" disabled 
                                                       {{ $question->correct_answer === 'd' ? 'checked' : '' }}>
                                                <label class="form-check-label {{ $question->correct_answer === 'd' ? 'text-success fw-bold' : '' }}">
                                                    D: {{ $question->answer_d }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-3">
                                        <strong>Correct Antwoord:</strong> 
                                        <span class="text-success">{{ $question->correct_answer }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-question-circle fa-2x text-muted mb-2"></i>
                            <p class="text-muted">Geen vragen toegevoegd aan deze quiz.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics Sidebar -->
        <div class="col-md-4">
            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistieken</h6>
                </div>
                <div class="card-body">
                    @if($quiz->attempts->count() > 0)
                        @php
                            $completedAttempts = $quiz->attempts->whereNotNull('completed_at');
                            $avgScore = $completedAttempts->avg('score');
                            $highestScore = $completedAttempts->max('score');
                            $lowestScore = $completedAttempts->min('score');
                        @endphp
                        
                        <div class="mb-3">
                            <strong>Gemiddelde Score:</strong><br>
                            @if($avgScore)
                                <span class="h4 text-{{ $avgScore >= 80 ? 'success' : ($avgScore >= 60 ? 'warning' : 'danger') }}">
                                    {{ round($avgScore, 1) }}%
                                </span>
                            @else
                                <span class="text-muted">Nog geen voltooide pogingen</span>
                            @endif
                        </div>

                        @if($completedAttempts->count() > 0)
                            <div class="mb-2">
                                <strong>Hoogste Score:</strong> 
                                <span class="text-success">{{ $highestScore }}%</span>
                            </div>
                            <div class="mb-3">
                                <strong>Laagste Score:</strong> 
                                <span class="text-danger">{{ $lowestScore }}%</span>
                            </div>
                        @endif

                        <div class="mb-2">
                            <strong>Slagingspercentage:</strong><br>
                            @php
                                $passedAttempts = $completedAttempts->where('score', '>=', 60)->count();
                                $passRate = $completedAttempts->count() > 0 ? ($passedAttempts / $completedAttempts->count()) * 100 : 0;
                            @endphp
                            <span class="text-{{ $passRate >= 80 ? 'success' : ($passRate >= 60 ? 'warning' : 'danger') }}">
                                {{ round($passRate, 1) }}% ({{ $passedAttempts }}/{{ $completedAttempts->count() }})
                            </span>
                        </div>
                    @else
                        <p class="text-muted">Nog geen pogingen voor deze quiz.</p>
                    @endif
                </div>
            </div>

            <!-- Recent Attempts -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Recente Pogingen</h6>
                </div>
                <div class="card-body">
                    @if($quiz->attempts->count() > 0)
                        @foreach($quiz->attempts->take(5) as $attempt)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    <strong>{{ $attempt->user->name }}</strong><br>
                                    <small class="text-muted">{{ $attempt->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="text-end">
                                    @if($attempt->completed_at && $attempt->score !== null)
                                        <span class="badge bg-{{ $attempt->score >= 80 ? 'success' : ($attempt->score >= 60 ? 'warning' : 'danger') }}">
                                            {{ $attempt->score }}%
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Bezig</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        
                        @if($quiz->attempts->count() > 5)
                            <div class="text-center mt-2">
                                <small class="text-muted">En {{ $quiz->attempts->count() - 5 }} meer...</small>
                            </div>
                        @endif
                    @else
                        <p class="text-muted mb-0">Nog geen pogingen.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
