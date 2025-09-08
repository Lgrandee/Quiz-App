@extends('layouts.app')

@section('title', 'Upload Multiple Choice Questions')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-upload"></i> Upload Multiple Choice Questions via CSV</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- CSV Format Instructions -->
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> CSV Formaat Instructies</h5>
                        <p>Je CSV bestand ondersteunt zowel <strong>multiple choice</strong> als <strong>open vragen</strong>:</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Multiple Choice Vragen:</h6>
                                <code>vraag_id;type;vraag;antwoord_a;antwoord_b;antwoord_c;juiste_antwoord</code>
                                
                                <div class="bg-light p-2 rounded mt-2">
                                    <small><code>
                                        Q001;multiple_choice;Hoofdstad Nederland?;Amsterdam;Rotterdam;Den Haag;a<br>
                                        Q002;multiple_choice;Aantal provincies?;10;11;12;c
                                    </code></small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-success">Open Vragen:</h6>
                                <code>vraag_id;type;vraag;juiste_antwoord</code>
                                
                                <div class="bg-light p-2 rounded mt-2">
                                    <small><code>
                                        Q003;open_question;Wat is de hoofdstad van Frankrijk?;Parijs<br>
                                        Q004;open_question;Hoeveel benen heeft een spin?;8
                                    </code></small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <strong>Belangrijk:</strong>
                            <ul class="mb-0">
                                <li><strong>Type kolom:</strong> 'multiple_choice' of 'open_question'</li>
                                <li><strong>Multiple choice:</strong> Juiste antwoord moet 'a', 'b', of 'c' zijn</li>
                                <li><strong>Open vragen:</strong> Juiste antwoord is vrije tekst (case-insensitive)</li>
                                <li><strong>Scheidingsteken:</strong> Puntkomma (;)</li>
                                <li><strong>Eerste rij:</strong> Kolomnamen</li>
                                <li><strong>Bestandsgrootte:</strong> Max 2MB</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Upload Form -->
                    <form action="{{ route('questions.store') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                        @csrf
                        <div class="mb-4">
                            <label for="csv_file" class="form-label">
                                <i class="fas fa-file-csv"></i> Kies je CSV bestand
                            </label>
                            <input type="file" 
                                   class="form-control @error('csv_file') is-invalid @enderror" 
                                   id="csv_file" 
                                   name="csv_file" 
                                   accept=".csv,.txt" 
                                   required>
                            @error('csv_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Ondersteunde formaten: CSV, TXT (max 2MB)
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('questions.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-arrow-left"></i> Terug naar Vragen
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Vragen
                            </button>
                        </div>
                    </form>

                    <!-- Download Template -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6><i class="fas fa-download"></i> Template Downloaden</h6>
                        <p class="mb-2">Download een voorbeeld CSV template om te beginnen:</p>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="downloadTemplate()">
                            <i class="fas fa-file-download"></i> Download CSV Template
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function downloadTemplate() {
    const csvContent = `vraag_id;type;vraag;antwoord_a;antwoord_b;antwoord_c;juiste_antwoord
Q001;multiple_choice;Wat is de hoofdstad van Nederland?;Amsterdam;Rotterdam;Den Haag;a
Q002;multiple_choice;Hoeveel provincies heeft Nederland?;10;11;12;c
Q003;open_question;Wat is de hoofdstad van Frankrijk?;;;Parijs
Q004;open_question;Hoeveel benen heeft een spin?;;;8
Q005;multiple_choice;Wat is de grootste rivier van Nederland?;Rijn;Maas;IJssel;a`;
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'questions_template.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection