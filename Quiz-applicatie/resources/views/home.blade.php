@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="text-center py-5">
    <h1 class="display-4">Welcome to the Question Manager</h1>
    <p class="lead">Easily manage your questions and quizzes.</p>
    @auth
        @if(auth()->user()->isTeacher())
            <a href="{{ route('teacher.questions.create') }}" class="btn btn-primary">
                Upload Questions
            </a>
        @else
            <a href="{{ route('student.dashboard') }}" class="btn btn-primary">
                Take Quiz
            </a>
        @endif
    @else
        <a href="{{ route('login') }}" class="btn btn-primary">
            Login to Start
        </a>
    @endauth
</div>
@endsection
