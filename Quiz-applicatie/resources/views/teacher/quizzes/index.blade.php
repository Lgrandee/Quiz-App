@extends('layouts.app')

@section('title', 'Quiz Beheer')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-list me-2"></i>Quiz Beheer</h2>
        <a href="{{ route('teacher.quizzes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Nieuwe Quiz
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($quizzes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Titel</th>
                                <th>Beschrijving</th>
                                <th>Vragen</th>
                                <th>Tijdslimiet</th>
                                <th>Pogingen</th>
                                <th>Gemaakt</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quizzes as $quiz)
                                <tr>
                                    <td>
                                        <strong>{{ $quiz->title }}</strong>
                                        @if($quiz->is_active)
                                            <span class="badge bg-success ms-2">Actief</span>
                                        @else
                                            <span class="badge bg-secondary ms-2">Inactief</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($quiz->description)
                                            {{ Str::limit($quiz->description, 50) }}
                                        @else
                                            <em class="text-muted">Geen beschrijving</em>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $quiz->questions->count() }} vragen</span>
                                    </td>
                                    <td>
                                        @if($quiz->time_limit)
                                            <span class="badge bg-warning text-dark">{{ $quiz->time_limit }} min</span>
                                        @else
                                            <span class="text-muted">Geen limiet</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $quiz->attempts->count() }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $quiz->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('teacher.quizzes.show', $quiz) }}" class="btn btn-outline-info" title="Bekijken">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="btn btn-outline-primary" title="Bewerken">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" title="Verwijderen" 
                                                    onclick="confirmDelete({{ $quiz->id }}, '{{ $quiz->title }}')">
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
                @if($quizzes->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        <nav aria-label="Quiz pagination">
                            <ul class="pagination pagination-sm">
                                {{-- Previous Page Link --}}
                                @if ($quizzes->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link">Vorige</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $quizzes->previousPageUrl() }}">Vorige</a></li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($quizzes->getUrlRange(1, $quizzes->lastPage()) as $page => $url)
                                    @if ($page == $quizzes->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($quizzes->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $quizzes->nextPageUrl() }}">Volgende</a></li>
                                @else
                                    <li class="page-item disabled"><span class="page-link">Volgende</span></li>
                                @endif
                            </ul>
                        </nav>
                    </div>

                    <div class="text-center mt-2">
                        <small class="text-muted">
                            Toont {{ $quizzes->firstItem() }} tot {{ $quizzes->lastItem() }} van {{ $quizzes->total() }} quizzes
                        </small>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nog geen quizzes</h5>
                    <p class="text-muted mb-3">Maak je eerste quiz om te beginnen.</p>
                    <a href="{{ route('teacher.quizzes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nieuwe Quiz Maken
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Quiz Verwijderen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Weet je zeker dat je de quiz "<span id="quizTitle"></span>" wilt verwijderen?</p>
                <p class="text-danger"><strong>Let op:</strong> Dit kan niet ongedaan worden gemaakt en alle bijbehorende pogingen worden ook verwijderd.</p>
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
function confirmDelete(quizId, quizTitle) {
    document.getElementById('quizTitle').textContent = quizTitle;
    document.getElementById('deleteForm').action = `/teacher/quizzes/${quizId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endsection
