@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-tachometer-alt me-2"></i>Docenten Dashboard</h2>
        <div class="btn-group">
            <a href="{{ route('teacher.questions.create') }}" class="btn btn-primary">
                <i class="fas fa-upload me-1"></i>Upload Vragen
            </a>
            <a href="{{ route('teacher.quizzes.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i>Nieuwe Quiz
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $totalQuizzes }}</h4>
                            <p class="mb-0">Totaal Quizzes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-list fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $totalQuestions }}</h4>
                            <p class="mb-0">Totaal Vragen</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-question-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $totalAttempts }}</h4>
                            <p class="mb-0">Quiz Pogingen</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-bar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $totalStudents }}</h4>
                            <p class="mb-0">Actieve Studenten</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Quizzes -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recente Quizzes</h5>
                    <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-sm btn-outline-primary">Alle Quizzes</a>
                </div>
                <div class="card-body">
                    @if($recentQuizzes->count() > 0)
                        @foreach($recentQuizzes as $quiz)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    <h6 class="mb-1">{{ $quiz->title }}</h6>
                                    <small class="text-muted">{{ $quiz->created_at->diffForHumans() }}</small>
                                </div>
                                <div>
                                    <span class="badge bg-secondary">{{ $quiz->attempts->count() }} pogingen</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-list fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Nog geen quizzes aangemaakt</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Students -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top Studenten</h5>
                    <a href="{{ route('teacher.students') }}" class="btn btn-sm btn-outline-success">Alle Studenten</a>
                </div>
                <div class="card-body">
                    @if($topStudents->count() > 0)
                        @foreach($topStudents as $student)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    <h6 class="mb-1">{{ $student->user->name }}</h6>
                                    <small class="text-muted">{{ $student->attempts_count }} pogingen</small>
                                </div>
                                <div>
                                    <span class="badge bg-{{ $student->avg_score >= 80 ? 'success' : ($student->avg_score >= 60 ? 'warning' : 'danger') }}">
                                        {{ round($student->avg_score, 1) }}%
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-users fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Nog geen student activiteit</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recente Activiteit</h5>
                </div>
                <div class="card-body">
                    @if($recentAttempts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Quiz</th>
                                        <th>Score</th>
                                        <th>Datum</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAttempts as $attempt)
                                        <tr>
                                            <td>{{ $attempt->user->name }}</td>
                                            <td>{{ $attempt->quiz->title }}</td>
                                            <td>
                                                @if($attempt->score !== null)
                                                    <span class="badge bg-{{ $attempt->score >= 80 ? 'success' : ($attempt->score >= 60 ? 'warning' : 'danger') }}">
                                                        {{ $attempt->score }}%
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">In Progress</span>
                                                @endif
                                            </td>
                                            <td>{{ $attempt->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($attempt->completed_at)
                                                    <span class="badge bg-success">Voltooid</span>
                                                @else
                                                    <span class="badge bg-warning">Bezig</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nog geen activiteit</h5>
                            <p class="text-muted">Studenten moeten eerst quizzes maken voordat hier activiteit verschijnt.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
