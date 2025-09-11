@extends('layouts.app')

@section('title', 'Quiz - ' . $quiz->title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>{{ $quiz->title }}</h4>
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
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                Quiz Indienen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
