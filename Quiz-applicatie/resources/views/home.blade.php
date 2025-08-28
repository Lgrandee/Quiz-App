@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="text-center py-5">
    <h1 class="display-4">Welcome to the Question Manager</h1>
    <p class="lead">Easily manage your questions and quizzes.</p>
    <a href="{{ route('questions.create') }}" class="btn btn-primary">
        <i class="fas fa-upload"></i> Upload Questions
    </a>
</div>
@endsection