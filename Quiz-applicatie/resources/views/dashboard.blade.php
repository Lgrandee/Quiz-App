@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Dashboard</h3>
                </div>
                <div class="card-body">
                    <h4>Welcome, {{ auth()->user()->name }}!</h4>
                    <p>This is your quiz application dashboard.</p>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <a href="{{ route('questions.index') }}" class="btn btn-primary btn-lg w-100">
                                Manage Questions
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('quiz.show', 1) }}" class="btn btn-success btn-lg w-100">
                                Take Quiz
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
