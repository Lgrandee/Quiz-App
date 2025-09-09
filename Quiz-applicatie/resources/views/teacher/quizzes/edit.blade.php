@extends('layouts.app')

@section('title', 'Quiz Bewerken')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-edit me-2"></i>Quiz Bewerken</h2>
        <div class="btn-group">
            <a href="{{ route('teacher.quizzes.show', $quiz) }}" class="btn btn-info">
                <i class="fas fa-eye me-1"></i>Bekijken
            </a>
            <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Terug
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('teacher.quizzes.update', $quiz) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Quiz Titel <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $quiz->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Beschrijving</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $quiz->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="time_limit" class="form-label">Tijdslimiet (minuten)</label>
                            <input type="number" class="form-control @error('time_limit') is-invalid @enderror" 
                                   id="time_limit" name="time_limit" value="{{ old('time_limit', $quiz->time_limit) }}" min="1" max="300">
                            <div class="form-text">Laat leeg voor geen tijdslimiet</div>
                            @error('time_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', $quiz->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Quiz is actief
                                </label>
                            </div>
                            <div class="form-text">Alleen actieve quizzes zijn zichtbaar voor studenten</div>
                        </div>
                    </div>
                </div>

                <hr>

                <h5><i class="fas fa-question-circle me-2"></i>Vragen Selecteren</h5>
                
                @if($questions->count() > 0)
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label" for="selectAll">
                                <strong>Alle vragen selecteren</strong>
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        @foreach($questions as $question)
                            <div class="col-md-6 mb-3">
                                <div class="card question-card">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input question-checkbox" type="checkbox" 
                                                   name="questions[]" value="{{ $question->id }}" 
                                                   id="question_{{ $question->id }}"
                                                   {{ in_array($question->id, old('questions', $quiz->questions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="question_{{ $question->id }}">
                                                <strong>{{ Str::limit($question->question_text, 60) }}</strong>
                                            </label>
                                        </div>
                                        
                                        <div class="mt-2">
                                            @if($question->type === 'multiple_choice')
                                                <span class="badge bg-primary">Multiple Choice</span>
                                            @else
                                                <span class="badge bg-info">Open Vraag</span>
                                            @endif
                                            
                                            @if($question->difficulty)
                                                <span class="badge bg-{{ $question->difficulty === 'easy' ? 'success' : ($question->difficulty === 'medium' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($question->difficulty) }}
                                                </span>
                                            @endif
                                        </div>

                                        @if($question->type === 'multiple_choice')
                                            <div class="mt-2 small text-muted">
                                                <div>A: {{ Str::limit($question->answer_a, 30) }}</div>
                                                <div>B: {{ Str::limit($question->answer_b, 30) }}</div>
                                                <div>C: {{ Str::limit($question->answer_c, 30) }}</div>
                                                <div>D: {{ Str::limit($question->answer_d, 30) }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @error('questions')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Er zijn nog geen vragen beschikbaar. 
                        <a href="{{ route('teacher.questions.create') }}" class="alert-link">Upload eerst vragen</a> 
                        voordat je een quiz kunt bewerken.
                    </div>
                @endif

                <!-- Existing Attempts Warning -->
                @if($quiz->attempts->count() > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Let op:</strong> Deze quiz heeft al {{ $quiz->attempts->count() }} poging(en). 
                        Wijzigingen aan de vragen kunnen de resultaten van bestaande pogingen beïnvloeden.
                    </div>
                @endif

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('teacher.quizzes.show', $quiz) }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>Annuleren
                    </a>
                    
                    @if($questions->count() > 0)
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Wijzigingen Opslaan
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const questionCheckboxes = document.querySelectorAll('.question-checkbox');

    // Initialize select all checkbox state
    updateSelectAllState();

    // Select/deselect all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            questionCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Update select all checkbox based on individual selections
        questionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllState();
            });
        });
    }

    function updateSelectAllState() {
        const checkedCount = Array.from(questionCheckboxes).filter(cb => cb.checked).length;
        const totalCount = questionCheckboxes.length;
        
        if (checkedCount === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedCount === totalCount) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
    }
});
</script>

<style>
.question-card {
    transition: all 0.2s ease;
    border: 1px solid #dee2e6;
}

.question-card:hover {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.question-checkbox:checked + label {
    font-weight: bold;
}

.question-card:has(.question-checkbox:checked) {
    border-color: #0d6efd;
    background-color: #f8f9ff;
}
</style>
@endsection
