@extends('layouts.app')

@section('title', 'Quiz Resultaten - ' . $quiz->title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Score Overview -->
            <div class="card mb-4">
                <div class="card-header bg-{{ $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger') }} text-white">
                    <h4><i class="fas fa-trophy"></i> Quiz Voltooid: {{ $quiz->title }}</h4>
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-md-4">
                            <h2 class="display-4 text-{{ $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger') }}">
                                {{ $percentage }}%
                            </h2>
                            <p class="text-muted">Eindresultaat</p>
                        </div>
                        <div class="col-md-4">
                            <h3>{{ $score }} / {{ $totalPoints }}</h3>
                            <p class="text-muted">Punten behaald</p>
                        </div>
                        <div class="col-md-4">
                            <h3>{{ count($results) }}</h3>
                            <p class="text-muted">Vragen beantwoord</p>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        @if($percentage >= 80)
                            <span class="badge bg-success fs-6">Uitstekend! 🎉</span>
                        @elseif($percentage >= 60)
                            <span class="badge bg-warning fs-6">Goed gedaan! 👍</span>
                        @else
                            <span class="badge bg-danger fs-6">Meer oefening nodig 📚</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detailed Results -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-list-alt"></i> Gedetailleerde Resultaten</h5>
                </div>
                <div class="card-body">
                    @foreach($results as $index => $result)
                        <div class="result-item mb-4 p-3 border rounded {{ $result['is_correct'] ? 'border-success bg-light-success' : 'border-danger bg-light-danger' }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0">
                                    <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                    {{ $result['question']->question_text }}
                                </h6>
                                <span class="badge bg-{{ $result['is_correct'] ? 'success' : 'danger' }}">
                                    {{ $result['is_correct'] ? 'Correct' : 'Fout' }}
                                    ({{ $result['question']->points }} pt)
                                </span>
                            </div>
                            
                            @if($result['question']->type === 'multiple_choice')
                                <div class="options mt-3">
                                    @foreach($result['question']->options as $key => $option)
                                        <div class="option-display mb-1 p-2 rounded
                                            @if($key === $result['user_answer'] && $result['is_correct']) bg-success text-white
                                            @elseif($key === $result['user_answer'] && !$result['is_correct']) bg-danger text-white
                                            @elseif($key === $result['correct_answer']) bg-success text-white opacity-75
                                            @else bg-light
                                            @endif">
                                            <strong>{{ strtoupper($key) }}.</strong> {{ $option }}
                                            @if($key === $result['user_answer'])
                                                <i class="fas fa-arrow-left ms-2"></i> <small>Jouw antwoord</small>
                                            @endif
                                            @if($key === $result['correct_answer'] && $key !== $result['user_answer'])
                                                <i class="fas fa-check ms-2"></i> <small>Juiste antwoord</small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <!-- Open Question Display -->
                                <div class="mt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="answer-box p-3 rounded {{ $result['is_correct'] ? 'bg-success bg-opacity-10 border border-success' : 'bg-danger bg-opacity-10 border border-danger' }}">
                                                <strong class="text-{{ $result['is_correct'] ? 'success' : 'danger' }}">
                                                    <i class="fas fa-{{ $result['is_correct'] ? 'check' : 'times' }}"></i> Jouw antwoord:
                                                </strong>
                                                <div class="mt-2 fw-bold">
                                                    "{{ $result['user_answer'] ?: 'Geen antwoord gegeven' }}"
                                                </div>
                                                @if(!empty($result['user_answer']) && !$result['is_correct'])
                                                    @php
                                                        $similarity = $result['question']->calculateAnswerSimilarity($result['user_answer']);
                                                    @endphp
                                                    <div class="mt-2">
                                                        @if($similarity > 0.7)
                                                            <small class="text-warning">
                                                                <i class="fas fa-exclamation-triangle"></i> 
                                                                Bijna goed! ({{ round($similarity * 100) }}% overeenkomst)
                                                            </small>
                                                        @elseif($similarity > 0.3)
                                                            <small class="text-info">
                                                                <i class="fas fa-info-circle"></i> 
                                                                Gedeeltelijk correct ({{ round($similarity * 100) }}% overeenkomst)
                                                            </small>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @if(!$result['is_correct'])
                                            <div class="col-md-6">
                                                <div class="answer-box p-3 rounded bg-success bg-opacity-10 border border-success">
                                                    <strong class="text-success">
                                                        <i class="fas fa-lightbulb"></i> Juiste antwoord:
                                                    </strong>
                                                    <div class="mt-2 fw-bold">
                                                        "{{ $result['correct_answer'] }}"
                                                    </div>
                                                    <small class="text-muted mt-2 d-block">
                                                        <i class="fas fa-info-circle"></i> 
                                                        Hoofdletters en spaties worden genegeerd
                                                    </small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="text-center mt-4 mb-4">
                <a href="{{ route('quiz.show', $quiz->id) }}" class="btn btn-primary me-2">
                    <i class="fas fa-redo"></i> Quiz Opnieuw Maken
                </a>
                <a href="{{ route('questions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Alle Vragen Bekijken
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.bg-light-success {
    background-color: #d1f2eb !important;
}

.bg-light-danger {
    background-color: #fadbd8 !important;
}

.option-display {
    transition: all 0.3s ease;
}

.result-item {
    transition: all 0.3s ease;
}
</style>
@endsection
