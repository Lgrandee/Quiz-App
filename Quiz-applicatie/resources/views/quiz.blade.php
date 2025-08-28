@extends('layouts.app')

@section('title', 'Quiz')

@section('content')
<div class="card">
    <div class="card-header">
        Answer All Questions (Multiple Choice)
    </div>
    <div class="card-body">
        <form action="{{ route('quiz.submit') }}" method="POST">
            @csrf
            @foreach($questions as $question)
                <div class="mb-3">
                    <label class="form-label"><strong>{{ $question->question }}</strong></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answer_{{ $question->id }}" id="answer_a_{{ $question->id }}" value="a" required>
                        <label class="form-check-label" for="answer_a_{{ $question->id }}">
                            {{ $question->answer_a }}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answer_{{ $question->id }}" id="answer_b_{{ $question->id }}" value="b">
                        <label class="form-check-label" for="answer_b_{{ $question->id }}">
                            {{ $question->answer_b }}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answer_{{ $question->id }}" id="answer_c_{{ $question->id }}" value="c">
                        <label class="form-check-label" for="answer_c_{{ $question->id }}">
                            {{ $question->answer_c }}
                        </label>
                    </div>
                </div>
            @endforeach
            <button type="submit" class="btn btn-success">Submit Answers</button>
        </form>
    </div>
</div>
@endsection
