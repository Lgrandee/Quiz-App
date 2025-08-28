@extends('layouts.app')

@section('title', 'Quiz Results')

@section('content')
<div class="card">
    <div class="card-header">
        Quiz Results
    </div>
    <div class="card-body">
        <h4>Your Score: {{ $score }} / {{ count($questions) }}</h4>
        <p>
            <span class="text-success">Correct: {{ $score }}</span> |
            <span class="text-danger">Incorrect: {{ count($questions) - $score }}</span>
        </p>
        <ul class="list-group mt-3">
            @foreach($results as $result)
                <li class="list-group-item">
                    <strong>{{ $result['question'] }}</strong><br>
                    Your answer: <span class="{{ $result['is_correct'] ? 'text-success' : 'text-danger' }}">{{ $result['user_answer'] }}</span><br>
                    Correct answer: <span class="text-success">{{ $result['correct_answer'] }}</span>
                </li>
            @endforeach
        </ul>
        <a href="{{ route('quiz.show') }}" class="btn btn-primary mt-3">Try Again</a>
    </div>
</div>
@endsection
