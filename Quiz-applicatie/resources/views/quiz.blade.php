@extends('layouts.app')

@section('title', 'Quiz - ' . $quiz->title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-question-circle"></i> {{ $quiz->title }}</h4>
                        <div class="quiz-info">
                            <span class="badge bg-light text-dark">{{ $quiz->questions->count() }} vragen</span>
                            @if($quiz->time_limit)
                                <span class="badge bg-warning text-dark">{{ $quiz->time_limit }} minuten</span>
                            @endif
                        </div>
                    </div>
                    @if($quiz->description)
                        <p class="mb-0 mt-2">{{ $quiz->description }}</p>
                    @endif
                </div>
                <div class="card-body">
                    <form action="{{ route('quiz.submit') }}" method="POST" id="quizForm">
                        @csrf
                        <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                        
                        @foreach($quiz->questions as $index => $question)
                            <div class="question-card mb-4 p-3 border rounded">
                                <div class="question-header mb-3">
                                    <h5 class="question-number">
                                        <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                        {{ $question->question_text }}
                                    </h5>
                                    <small class="text-muted">{{ $question->points }} punt(en)</small>
                                </div>
                                
                                @if($question->type === 'multiple_choice')
                                    <div class="options">
                                        @foreach($question->options as $key => $option)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" 
                                                       type="radio" 
                                                       name="answer_{{ $question->id }}" 
                                                       id="answer_{{ $key }}_{{ $question->id }}" 
                                                       value="{{ $key }}" 
                                                       required>
                                                <label class="form-check-label" for="answer_{{ $key }}_{{ $question->id }}">
                                                    <strong>{{ strtoupper($key) }}.</strong> {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="form-group">
                                        <textarea class="form-control" 
                                                  name="answer_{{ $question->id }}" 
                                                  rows="3" 
                                                  placeholder="Typ hier je antwoord..."
                                                  required></textarea>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
                            <div class="quiz-progress">
                                <small class="text-muted">
                                    <i class="fas fa-tasks"></i> 
                                    <span id="answeredCount">0</span> van {{ $quiz->questions->count() }} vragen beantwoord
                                </small>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                <i class="fas fa-paper-plane"></i> Quiz Indienen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('quizForm');
    const submitBtn = document.getElementById('submitBtn');
    const answeredCount = document.getElementById('answeredCount');
    const totalQuestions = {{ $quiz->questions->count() }};
    
    // Track answered questions
    function updateProgress() {
        const answeredQuestions = form.querySelectorAll('input[type="radio"]:checked, textarea:not(:empty)').length;
        answeredCount.textContent = answeredQuestions;
        
        if (answeredQuestions === totalQuestions) {
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-success');
            submitBtn.disabled = false;
        } else {
            submitBtn.classList.remove('btn-success');
            submitBtn.classList.add('btn-secondary');
        }
    }
    
    // Listen for changes
    form.addEventListener('change', updateProgress);
    form.addEventListener('input', updateProgress);
    
    // Initial check
    updateProgress();
    
    // Confirm before submit
    form.addEventListener('submit', function(e) {
        const answeredQuestions = form.querySelectorAll('input[type="radio"]:checked, textarea:not(:empty)').length;
        
        if (answeredQuestions < totalQuestions) {
            if (!confirm(`Je hebt nog ${totalQuestions - answeredQuestions} vragen niet beantwoord. Weet je zeker dat je wilt indienen?`)) {
                e.preventDefault();
            }
        }
    });
});
</script>

<style>
.question-card {
    transition: all 0.3s ease;
}

.question-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.form-check-input:checked + .form-check-label {
    font-weight: bold;
    color: #0d6efd;
}

.question-number {
    color: #495057;
}

.badge {
    font-size: 0.8em;
}
</style>
@endsection
