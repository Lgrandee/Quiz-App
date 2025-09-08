@extends('layouts.app')

@section('title', 'All Questions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Questions Database</h2>
    <a href="{{ route('questions.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Upload New CSV
    </a>
</div>

@if($questions->count() > 0)
    <div class="row">
        @foreach($questions as $question)
            <div class="col-md-6 mb-4">
                <div class="card question-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="badge bg-secondary">ID: {{ $question->question_id }}</span>
                        <small class="text-muted">{{ $question->created_at->format('M d, Y') }}</small>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">{{ $question->question }}</h6>
                        
                        <div class="mt-3">
                            <div class="mb-2">
                                <span class="badge bg-light text-dark me-2">A</span>
                                <span class="{{ $question->correct_answer == 'a' ? 'correct-answer px-2 py-1 rounded' : '' }}">
                                    {{ $question->answer_a }}
                                </span>
                            </div>
                            <div class="mb-2">
                                <span class="badge bg-light text-dark me-2">B</span>
                                <span class="{{ $question->correct_answer == 'b' ? 'correct-answer px-2 py-1 rounded' : '' }}">
                                    {{ $question->answer_b }}
                                </span>
                            </div>
                            <div class="mb-2">
                                <span class="badge bg-light text-dark me-2">C</span>
                                <span class="{{ $question->correct_answer == 'c' ? 'correct-answer px-2 py-1 rounded' : '' }}">
                                    {{ $question->answer_c }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="mt-3 pt-2 border-top">
                            <small class="text-success">
                                <strong>Correct Answer: {{ strtoupper($question->correct_answer) }}</strong>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Beautiful Custom Pagination -->
    @if($questions->hasPages())
        <div class="pagination-wrapper mt-5">
            <div class="pagination-info text-center mb-3">
                <span class="badge bg-light text-dark px-3 py-2">
                    <i class="fas fa-info-circle me-1"></i>
                    Showing {{ $questions->firstItem() }} to {{ $questions->lastItem() }} of {{ $questions->total() }} questions
                </span>
            </div>
            
            <nav aria-label="Questions pagination" class="d-flex justify-content-center">
                <ul class="pagination pagination-lg custom-pagination">
                    {{-- Previous Page Link --}}
                    @if ($questions->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="fas fa-chevron-left"></i>
                                <span class="d-none d-sm-inline ms-1">Previous</span>
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $questions->previousPageUrl() }}" rel="prev">
                                <i class="fas fa-chevron-left"></i>
                                <span class="d-none d-sm-inline ms-1">Previous</span>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($questions->getUrlRange(1, $questions->lastPage()) as $page => $url)
                        @if ($page == $questions->currentPage())
                            <li class="page-item active">
                                <span class="page-link current-page">
                                    {{ $page }}
                                    <span class="sr-only">(current)</span>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($questions->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $questions->nextPageUrl() }}" rel="next">
                                <span class="d-none d-sm-inline me-1">Next</span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">
                                <span class="d-none d-sm-inline me-1">Next</span>
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    @endif

@else
    <div class="text-center py-5">
        <div class="mb-4">
            <i class="fas fa-question-circle fa-3x text-muted"></i>
        </div>
        <h4 class="text-muted">No Questions Found</h4>
        <p class="text-muted">Upload a CSV file to get started with your questions database.</p>
        <a href="{{ route('questions.create') }}" class="btn btn-primary">
            <i class="fas fa-upload"></i> Upload Your First CSV
        </a>
    </div>
@endif
@endsection