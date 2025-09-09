@extends('layouts.app')

@section('title', 'Quiz - ' . $quiz->title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Progress Bar -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">{{ $quiz->title }}</h6>
                        <div class="quiz-status">
                            <span class="text-muted">Vraag {{ $currentQuestion }} van {{ $totalQuestions }}</span>
                            <span class="badge bg-info ms-2">{{ count($answers) }} beantwoord</span>
                        </div>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-gradient" 
                             role="progressbar" 
                             style="width: {{ ($currentQuestion / $totalQuestions) * 100 }}%; background: linear-gradient(90deg, #007bff 0%, #28a745 100%);"
                             aria-valuenow="{{ $currentQuestion }}" 
                             aria-valuemin="0" 
                             aria-valuemax="{{ $totalQuestions }}">
                        </div>
                    </div>
                    <div class="mt-2 d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-chart-line"></i> 
                            {{ round(($currentQuestion / $totalQuestions) * 100, 1) }}% doorlopen
                        </small>
                        <small class="text-muted">
                            <i class="fas fa-check-circle text-success"></i> 
                            {{ count($answers) }}/{{ $totalQuestions }} opgeslagen
                        </small>
                    </div>
                </div>
            </div>

            <!-- Question Card -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-question-circle"></i> 
                            Vraag {{ $currentQuestion }}
                        </h5>
                        <span class="badge bg-light text-dark">{{ $question->points }} punt(en)</span>
                    </div>
                </div>
                <div class="card-body">
                    <form id="questionForm" method="POST">
                        @csrf
                        <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                        <input type="hidden" name="question_number" value="{{ $currentQuestion }}">
                        <input type="hidden" name="current_answers" value="{{ json_encode($answers) }}">
                        
                        <!-- Question Text -->
                        <div class="question-container mb-4">
                            <h4 class="question-text">{{ $question->question_text }}</h4>
                        </div>

                        @if($question->type === 'multiple_choice')
                            <!-- Multiple Choice Options -->
                            <div class="options-container">
                                @foreach($question->options as $key => $option)
                                    <div class="option-card mb-3 p-3 border rounded" 
                                         onclick="selectOption('{{ $key }}')" 
                                         id="option_{{ $key }}">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="answer" 
                                                   id="answer_{{ $key }}" 
                                                   value="{{ $key }}"
                                                   {{ isset($answers[$question->id]) && $answers[$question->id] == $key ? 'checked' : '' }}>
                                            <label class="form-check-label w-100" for="answer_{{ $key }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="option-letter me-3">{{ strtoupper($key) }}</span>
                                                    <span class="option-text">{{ $option }}</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Open Question -->
                            <div class="form-group">
                                <label class="form-label mb-2">
                                    <i class="fas fa-edit"></i> Typ je antwoord hieronder:
                                </label>
                                <textarea class="form-control open-question-input" 
                                          name="answer" 
                                          rows="4" 
                                          placeholder="Typ hier je antwoord..."
                                          maxlength="500"
                                          oninput="updateCharacterCount(this)"
                                          onblur="validateOpenAnswer(this)"
                                          required>{{ $answers[$question->id] ?? '' }}</textarea>
                                <div class="d-flex justify-content-between mt-2">
                                    <small class="text-muted">
                                        <span id="charCount">{{ strlen($answers[$question->id] ?? '') }}</span>/500 karakters
                                    </small>
                                    <div id="answerFeedback" class="feedback-container"></div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-info">
                                        <i class="fas fa-info-circle"></i> 
                                        Tip: Hoofdletters en spaties worden genegeerd bij de controle
                                    </small>
                                </div>
                            </div>
                        @endif

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <div>
                                @if($currentQuestion > 1)
                                    <button type="button" class="btn btn-outline-secondary" onclick="previousQuestion()">
                                        <i class="fas fa-arrow-left"></i> Vorige
                                    </button>
                                @endif
                            </div>
                            
                            <div class="text-center">
                                <small class="text-muted d-block mb-2">
                                    <i class="fas fa-save text-success"></i> 
                                    Automatisch opgeslagen
                                </small>
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="pauseQuiz()">
                                    <i class="fas fa-pause"></i> Quiz Pauzeren
                                </button>
                            </div>
                            
                            <div>
                                @if($currentQuestion < $totalQuestions)
                                    <button type="button" class="btn btn-primary" onclick="nextQuestion()" id="nextBtn">
                                        Volgende <i class="fas fa-arrow-right"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-success" onclick="finishQuiz()" id="finishBtn">
                                        <i class="fas fa-flag-checkered"></i> Quiz Voltooien
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quiz Overview -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6>Quiz Overzicht</h6>
                    <div class="question-overview d-flex flex-wrap gap-2">
                        @for($i = 1; $i <= $totalQuestions; $i++)
                            <div class="question-indicator 
                                {{ $i == $currentQuestion ? 'current' : '' }}
                                {{ isset($answers[$quiz->questions[$i-1]->id]) ? 'answered' : 'unanswered' }}"
                                onclick="goToQuestion({{ $i }})">
                                {{ $i }}
                            </div>
                        @endfor
                    </div>
                    <small class="text-muted mt-2 d-block">
                        Klik op een nummer om naar die vraag te gaan
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectOption(key) {
    // Remove selection from all options
    document.querySelectorAll('.option-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selection to clicked option
    document.getElementById('option_' + key).classList.add('selected');
    document.getElementById('answer_' + key).checked = true;
    
    // Auto-save answer when user changes selection
    saveAnswer();
}

function saveAnswer() {
    const form = document.getElementById('questionForm');
    const formData = new FormData(form);
    
    fetch('{{ route("student.quiz.save-answer") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update saved answers count in progress bar
            updateProgressBar();
            
            // Show visual feedback
            showSaveConfirmation();
        }
    })
    .catch(error => {
        console.error('Error saving answer:', error);
    });
}

function updateCharacterCount(textarea) {
    const charCount = document.getElementById('charCount');
    if (charCount) {
        const currentLength = textarea.value.length;
        charCount.textContent = currentLength;
        
        // Change color based on character usage
        if (currentLength > 400) {
            charCount.className = 'text-danger';
        } else if (currentLength > 300) {
            charCount.className = 'text-warning';
        } else {
            charCount.className = 'text-muted';
        }
    }
    
    // Auto-save for open questions
    saveAnswer();
}

function validateOpenAnswer(textarea) {
    const correctAnswer = '{{ $question->correct_answer ?? "" }}';
    const userAnswer = textarea.value.trim();
    provideOpenQuestionFeedback(userAnswer, correctAnswer, textarea);
}

function provideOpenQuestionFeedback(userAnswer, correctAnswer, textarea) {
    const feedbackContainer = document.getElementById('answerFeedback');
    
    if (!feedbackContainer || !correctAnswer || !userAnswer) {
        return;
    }
    
    // Performance optimization: Use requestAnimationFrame for smooth UI updates
    requestAnimationFrame(() => {
        const startTime = performance.now();
        
        // Case-insensitive comparison, ignoring spaces
        const normalizedCorrect = correctAnswer.toLowerCase().replace(/\s+/g, '');
        const normalizedUser = userAnswer.toLowerCase().replace(/\s+/g, '');
        
        if (normalizedUser === normalizedCorrect) {
            feedbackContainer.innerHTML = `
                <span class="badge bg-success">
                    <i class="fas fa-check"></i> Correct!
                </span>
            `;
            textarea.classList.remove('is-invalid');
            textarea.classList.add('is-valid');
        } else if (normalizedUser.length > 0) {
            // Show partial feedback for non-empty answers
            const similarity = calculateSimilarity(normalizedCorrect, normalizedUser);
            if (similarity > 0.7) {
                feedbackContainer.innerHTML = `
                    <span class="badge bg-warning">
                        <i class="fas fa-exclamation-triangle"></i> Bijna goed
                    </span>
                `;
            } else {
                feedbackContainer.innerHTML = `
                    <span class="badge bg-danger">
                        <i class="fas fa-times"></i> Probeer opnieuw
                    </span>
                `;
            }
            textarea.classList.remove('is-valid');
            textarea.classList.add('is-invalid');
        } else {
            feedbackContainer.innerHTML = '';
            textarea.classList.remove('is-valid', 'is-invalid');
        }
        
        // Log feedback timing for performance monitoring
        const endTime = performance.now();
        const feedbackTime = endTime - startTime;
        if (feedbackTime > 100) { // Log if feedback takes more than 100ms
            console.warn(`Feedback took ${feedbackTime.toFixed(2)}ms - consider optimization`);
        }
    });
}

function calculateSimilarity(str1, str2) {
    const longer = str1.length > str2.length ? str1 : str2;
    const shorter = str1.length > str2.length ? str2 : str1;
    
    if (longer.length === 0) {
        return 1.0;
    }
    
    const editDistance = levenshteinDistance(longer, shorter);
    return (longer.length - editDistance) / longer.length;
}

function levenshteinDistance(str1, str2) {
    const matrix = [];
    
    for (let i = 0; i <= str2.length; i++) {
        matrix[i] = [i];
    }
    
    for (let j = 0; j <= str1.length; j++) {
        matrix[0][j] = j;
    }
    
    for (let i = 1; i <= str2.length; i++) {
        for (let j = 1; j <= str1.length; j++) {
            if (str2.charAt(i - 1) === str1.charAt(j - 1)) {
                matrix[i][j] = matrix[i - 1][j - 1];
            } else {
                matrix[i][j] = Math.min(
                    matrix[i - 1][j - 1] + 1,
                    matrix[i][j - 1] + 1,
                    matrix[i - 1][j] + 1
                );
            }
        }
    }
    
    return matrix[str2.length][str1.length];
}

function nextQuestion() {
    saveAnswer();
    const nextQuestionNumber = {{ $currentQuestion }} + 1;
    window.location.href = `{{ url('quiz') }}/{{ $quiz->id }}/question/${nextQuestionNumber}`;
}

function previousQuestion() {
    saveAnswer();
    const prevQuestionNumber = {{ $currentQuestion }} - 1;
    window.location.href = `{{ url('quiz') }}/{{ $quiz->id }}/question/${prevQuestionNumber}`;
}

function goToQuestion(questionNumber) {
    saveAnswer();
    window.location.href = `{{ url('quiz') }}/{{ $quiz->id }}/question/${questionNumber}`;
}

function pauseQuiz() {
    saveAnswer();
    if (confirm('Wil je de quiz pauzeren? Je kunt later hervatten waar je gebleven bent.')) {
        window.location.href = '{{ route("student.dashboard") }}';
    }
}

function finishQuiz() {
    saveAnswer();
    
    // Check if all questions are answered
    const totalQuestions = {{ $totalQuestions }};
    const answeredQuestions = Object.keys(getSessionAnswers()).length;
    
    if (answeredQuestions < totalQuestions) {
        const unansweredCount = totalQuestions - answeredQuestions;
        showIncompleteWarning(unansweredCount, totalQuestions);
        return;
    }
    
    // Enhanced confirmation dialog
    const confirmMessage = `🎯 Quiz Voltooien\n\n` +
                          `✅ Alle ${totalQuestions} vragen zijn beantwoord\n` +
                          `⚠️  Na indienen zijn geen wijzigingen meer mogelijk\n\n` +
                          `Weet je zeker dat je de quiz definitief wilt indienen?`;
    
    if (confirm(confirmMessage)) {
        // Prevent any further changes
        disableAllInputs();
        
        // Show loading state
        const finishBtn = document.getElementById('finishBtn');
        if (finishBtn) {
            finishBtn.disabled = true;
            finishBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Indienen...';
        }
        
        // Add submission timestamp
        const timestampInput = document.createElement('input');
        timestampInput.type = 'hidden';
        timestampInput.name = 'submission_timestamp';
        timestampInput.value = new Date().toISOString();
        document.getElementById('questionForm').appendChild(timestampInput);
        
        document.getElementById('questionForm').action = '{{ route("student.quiz.submit") }}';
        document.getElementById('questionForm').submit();
    }
}

function showIncompleteWarning(unansweredCount, totalQuestions) {
    const warningMessage = `⚠️ Quiz Niet Compleet\n\n` +
                          `Je hebt nog ${unansweredCount} van de ${totalQuestions} vragen niet beantwoord.\n\n` +
                          `Ga terug naar de onbeantwoorde vragen om je quiz te voltooien.`;
    
    alert(warningMessage);
    
    // Highlight unanswered questions
    highlightUnansweredQuestions();
}

function highlightUnansweredQuestions() {
    const sessionAnswers = getSessionAnswers();
    const indicators = document.querySelectorAll('.question-indicator');
    
    indicators.forEach((indicator, index) => {
        const questionNumber = index + 1;
        const questionId = getQuestionIdByNumber(questionNumber);
        
        if (!sessionAnswers[questionId]) {
            indicator.classList.add('unanswered-warning');
            indicator.title = 'Deze vraag is nog niet beantwoord';
        }
    });
    
    // Remove highlights after 5 seconds
    setTimeout(() => {
        indicators.forEach(indicator => {
            indicator.classList.remove('unanswered-warning');
        });
    }, 5000);
}

function getSessionAnswers() {
    // Get answers from session storage or current page state
    const answers = {};
    @foreach($quiz->questions as $question)
        @if(isset($answers[$question->id]))
            answers[{{ $question->id }}] = '{{ $answers[$question->id] }}';
        @endif
    @endforeach
    return answers;
}

function getQuestionIdByNumber(questionNumber) {
    const questions = [
        @foreach($quiz->questions as $question)
            {{ $question->id }},
        @endforeach
    ];
    return questions[questionNumber - 1];
}

function disableAllInputs() {
    // Disable all form inputs to prevent changes after submission
    const allInputs = document.querySelectorAll('input, textarea, button, select');
    allInputs.forEach(input => {
        if (input.type !== 'hidden') {
            input.disabled = true;
            input.style.opacity = '0.6';
            input.style.pointerEvents = 'none';
        }
    });
    
    // Add visual indication that quiz is submitted
    const container = document.querySelector('.container');
    if (container) {
        container.classList.add('submit-loading');
    }
    
    // Show submission overlay
    showSubmissionOverlay();
}

function showSubmissionOverlay() {
    const overlay = document.createElement('div');
    overlay.id = 'submissionOverlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(2px);
    `;
    
    overlay.innerHTML = `
        <div style="
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            max-width: 400px;
            margin: 1rem;
        ">
            <div style="font-size: 3rem; color: #28a745; margin-bottom: 1rem;">
                <i class="fas fa-check-circle"></i>
            </div>
            <h4 style="color: #333; margin-bottom: 1rem;">Quiz Ingediend!</h4>
            <p style="color: #666; margin-bottom: 1.5rem;">
                Je antwoorden worden verwerkt...<br>
                <small>Je wordt automatisch doorgestuurd naar de resultaten.</small>
            </p>
            <div style="display: flex; align-items: center; justify-content: center; color: #007bff;">
                <i class="fas fa-spinner fa-spin" style="margin-right: 0.5rem;"></i>
                Verwerken...
            </div>
        </div>
    `;
    
    document.body.appendChild(overlay);
}

// Auto-select previously selected option on page load
document.addEventListener('DOMContentLoaded', function() {
    const checkedOption = document.querySelector('input[name="answer"]:checked');
    if (checkedOption) {
        selectOption(checkedOption.value);
    }
});

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowLeft' && {{ $currentQuestion }} > 1) {
        previousQuestion();
    } else if (e.key === 'ArrowRight' && {{ $currentQuestion }} < {{ $totalQuestions }}) {
        nextQuestion();
    } else if (e.key >= '1' && e.key <= '3') {
        const options = ['a', 'b', 'c'];
        const optionIndex = parseInt(e.key) - 1;
        if (options[optionIndex] && document.getElementById('answer_' + options[optionIndex])) {
            selectOption(options[optionIndex]);
        }
    }
});
</script>

<style>
.option-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #dee2e6 !important;
}

.option-card:hover {
    border-color: #0d6efd;
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.1);
    transform: translateY(-2px);
}

.option-card.selected {
    border-color: #0d6efd;
    background-color: #e7f3ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
}

.open-question-input {
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 12px;
    font-size: 16px;
    transition: all 0.3s ease;
    resize: vertical;
    min-height: 100px;
}

.open-question-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.open-question-input.is-valid {
    border-color: #198754;
    background-color: #f8fff9;
}

.open-question-input.is-invalid {
    border-color: #dc3545;
    background-color: #fff8f8;
}

.feedback-container {
    min-height: 24px;
    display: flex;
    align-items: center;
}

.feedback-container .badge {
    font-size: 0.75rem;
    padding: 4px 8px;
}

.unanswered-warning {
    animation: pulse-warning 1s infinite;
    border: 2px solid #dc3545 !important;
    background-color: #fff5f5 !important;
}

@keyframes pulse-warning {
    0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}

.submit-loading {
    opacity: 0.7;
    pointer-events: none;
}

.question-indicator {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease;
    border: 2px solid #dee2e6;
    background: white;
}

.question-indicator.answered {
    background: #28a745;
    color: white;
    border-color: #28a745;
}

.question-indicator.current {
    background: #007bff;
    color: white;
    border-color: #007bff;
    transform: scale(1.1);
}

.question-indicator:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.progress-bar {
    transition: width 0.5s ease;
}

.question-text {
    color: #2c3e50;
    line-height: 1.6;
}

.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}
</style>
@endsection
