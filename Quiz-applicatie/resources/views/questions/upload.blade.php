@extends('layouts.app')

@section('title', 'Upload Questions CSV')

@section('content')
<div class="card">
    <div class="card-header">
        Upload Questions CSV
    </div>
    <div class="card-body">
        <form action="{{ route('questions.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="csv_file" class="form-label">Choose CSV File</label>
                <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</div>
@endsection