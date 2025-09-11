@extends('layouts.app')

@section('title', 'Vragen Beheer')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-question-circle me-2"></i>Vragen Beheer</h2>
        <div class="btn-group">
            <a href="{{ route('teacher.questions.create') }}" class="btn btn-primary">
                <i class="fas fa-upload me-1"></i>Upload Vragen
            </a>
            <a href="{{ route('teacher.questions.create-open') }}" class="btn btn-success">
                <i class="fas fa-edit me-1"></i>Upload Open Vragen
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($questions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Vraag</th>
                                <th>Type</th>
                                <th>Quiz</th>
                                <th>Punten</th>
                                <th>Aangemaakt</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($questions as $question)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $question->id }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ Str::limit($question->question_text, 60) }}</strong>
                                        @if($question->type === 'multiple_choice')
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    @if($question->options)
                                                        A: {{ Str::limit($question->options['a'] ?? '', 20) }} |
                                                        B: {{ Str::limit($question->options['b'] ?? '', 20) }} |
                                                        C: {{ Str::limit($question->options['c'] ?? '', 20) }}
                                                    @endif
                                                </small>
                                            </div>
                                            <div class="mt-1">
                                                <small class="text-success">
                                                    <i class="fas fa-check"></i> Correct: {{ strtoupper($question->correct_answer) }}
                                                </small>
                                            </div>
                                        @else
                                            <div class="mt-1">
                                                <small class="text-success">
                                                    <i class="fas fa-check"></i> Antwoord: {{ Str::limit($question->correct_answer, 30) }}
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($question->type === 'multiple_choice')
                                            <span class="badge bg-primary">Multiple Choice</span>
                                        @else
                                            <span class="badge bg-info">Open Vraag</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($question->quiz)
                                            <a href="{{ route('teacher.quizzes.show', $question->quiz) }}" class="text-decoration-none">
                                                {{ Str::limit($question->quiz->title, 30) }}
                                            </a>
                                        @else
                                            <span class="text-muted">Geen quiz</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark">{{ $question->points ?? 1 }} pt</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $question->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-info" title="Bekijken" 
                                                    onclick="viewQuestion({{ $question->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-primary" title="Bewerken" 
                                                    onclick="editQuestion({{ $question->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" title="Verwijderen" 
                                                    onclick="confirmDelete({{ $question->id }}, '{{ addslashes($question->question_text) }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($questions->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        <nav aria-label="Questions pagination">
                            <ul class="pagination pagination-sm">
                                {{-- Previous Page Link --}}
                                @if ($questions->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link">Vorige</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $questions->previousPageUrl() }}">Vorige</a></li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($questions->getUrlRange(1, $questions->lastPage()) as $page => $url)
                                    @if ($page == $questions->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($questions->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $questions->nextPageUrl() }}">Volgende</a></li>
                                @else
                                    <li class="page-item disabled"><span class="page-link">Volgende</span></li>
                                @endif
                            </ul>
                        </nav>
                    </div>

                    <div class="text-center mt-2">
                        <small class="text-muted">
                            Toont {{ $questions->firstItem() }} tot {{ $questions->lastItem() }} van {{ $questions->total() }} vragen
                        </small>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nog geen vragen</h5>
                    <p class="text-muted mb-3">Upload je eerste vragen om te beginnen.</p>
                    <div class="btn-group">
                        <a href="{{ route('teacher.questions.create') }}" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i>Upload Multiple Choice
                        </a>
                        <a href="{{ route('teacher.questions.create-open') }}" class="btn btn-success">
                            <i class="fas fa-edit me-1"></i>Upload Open Vragen
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Question View Modal -->
<div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="questionModalLabel">Vraag Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="questionModalBody">
                <!-- Question details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sluiten</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Vraag Verwijderen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Weet je zeker dat je deze vraag wilt verwijderen?</p>
                <p class="text-muted"><strong>Vraag:</strong> <span id="questionText"></span></p>
                <p class="text-danger"><strong>Let op:</strong> Dit kan niet ongedaan worden gemaakt.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Verwijderen</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function viewQuestion(questionId) {
    // You can implement AJAX to load question details
    // For now, we'll show a placeholder
    document.getElementById('questionModalBody').innerHTML = '<p>Loading question details...</p>';
    const modal = new bootstrap.Modal(document.getElementById('questionModal'));
    modal.show();
}

function editQuestion(questionId) {
    // Redirect to edit page (when implemented)
    alert('Edit functionality will be implemented soon.');
}

function confirmDelete(questionId, questionText) {
    document.getElementById('questionText').textContent = questionText;
    document.getElementById('deleteForm').action = `/teacher/questions/${questionId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endsection
