@extends('layouts.app')

@section('title', 'Question Details')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Question Details</h3>
                    <div>
                        <a href="{{ route('questions.edit', $question) }}" class="btn btn-primary btn-sm">Edit</a>
                        <a href="{{ route('questions.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Question:</strong>
                        <p>{{ $question->question_text }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Type:</strong>
                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $question->type)) }}</span>
                    </div>

                    @if($question->type === 'multiple_choice' && $question->options)
                    <div class="mb-3">
                        <strong>Options:</strong>
                        <ul class="list-group">
                            <li class="list-group-item {{ $question->correct_answer == 'a' ? 'list-group-item-success' : '' }}">
                                <strong>A:</strong> {{ $question->options['a'] ?? '' }}
                                @if($question->correct_answer == 'a') <span class="badge bg-success ms-2">Correct</span> @endif
                            </li>
                            <li class="list-group-item {{ $question->correct_answer == 'b' ? 'list-group-item-success' : '' }}">
                                <strong>B:</strong> {{ $question->options['b'] ?? '' }}
                                @if($question->correct_answer == 'b') <span class="badge bg-success ms-2">Correct</span> @endif
                            </li>
                            <li class="list-group-item {{ $question->correct_answer == 'c' ? 'list-group-item-success' : '' }}">
                                <strong>C:</strong> {{ $question->options['c'] ?? '' }}
                                @if($question->correct_answer == 'c') <span class="badge bg-success ms-2">Correct</span> @endif
                            </li>
                        </ul>
                    </div>
                    @else
                    <div class="mb-3">
                        <strong>Correct Answer:</strong>
                        <p class="text-success">{{ $question->correct_answer }}</p>
                    </div>
                    @endif

                    <div class="mb-3">
                        <strong>Points:</strong>
                        <span class="badge bg-warning text-dark">{{ $question->points ?? 1 }}</span>
                    </div>

                    @if($question->quiz)
                    <div class="mb-3">
                        <strong>Quiz:</strong>
                        <a href="{{ route('quizzes.show', $question->quiz) }}" class="text-decoration-none">
                            {{ $question->quiz->title }}
                        </a>
                    </div>
                    @endif

                    <div class="mb-3">
                        <strong>Created:</strong>
                        <small class="text-muted">{{ $question->created_at->format('M d, Y H:i') }}</small>
                    </div>

                    @if($question->updated_at != $question->created_at)
                    <div class="mb-3">
                        <strong>Last Updated:</strong>
                        <small class="text-muted">{{ $question->updated_at->format('M d, Y H:i') }}</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
