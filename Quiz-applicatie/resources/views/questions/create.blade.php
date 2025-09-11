@extends('layouts.app')

@section('title', 'Create Question')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Create New Question</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('questions.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="quiz_id" class="form-label">Quiz</label>
                            <select name="quiz_id" id="quiz_id" class="form-control" required>
                                <option value="">Select a quiz</option>
                                @foreach($quizzes as $quiz)
                                    <option value="{{ $quiz->id }}">{{ $quiz->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="question_text" class="form-label">Question Text</label>
                            <textarea name="question_text" id="question_text" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Question Type</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="open_question">Open Question</option>
                            </select>
                        </div>

                        <div id="multiple_choice_options" class="mb-3" style="display: none;">
                            <h5>Answer Options</h5>
                            <div class="mb-2">
                                <label for="option_a" class="form-label">Option A</label>
                                <input type="text" name="option_a" id="option_a" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label for="option_b" class="form-label">Option B</label>
                                <input type="text" name="option_b" id="option_b" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label for="option_c" class="form-label">Option C</label>
                                <input type="text" name="option_c" id="option_c" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="correct_answer" class="form-label">Correct Answer</label>
                            <input type="text" name="correct_answer" id="correct_answer" class="form-control" required>
                            <small class="form-text text-muted">
                                For multiple choice: enter 'a', 'b', or 'c'. For open questions: enter the correct answer text.
                            </small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('questions.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Question</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('type').addEventListener('change', function() {
    const multipleChoiceOptions = document.getElementById('multiple_choice_options');
    const correctAnswerInput = document.getElementById('correct_answer');

    if (this.value === 'multiple_choice') {
        multipleChoiceOptions.style.display = 'block';
        correctAnswerInput.placeholder = "Enter 'a', 'b', or 'c'";
    } else {
        multipleChoiceOptions.style.display = 'none';
        correctAnswerInput.placeholder = "Enter the correct answer";
    }
});
</script>
@endsection
