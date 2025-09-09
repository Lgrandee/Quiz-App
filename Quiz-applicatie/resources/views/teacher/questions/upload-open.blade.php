@extends('layouts.app')

@section('title', 'Open Vragen Uploaden')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4><i class="fas fa-edit"></i> Open Vragen CSV Upload</h4>
                    <p class="mb-0">Upload een CSV bestand met alleen open vragen</p>
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
                            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle"></i> Validatie Fouten:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- CSV Format Instructions for Open Questions -->
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Open Vragen CSV Formaat</h5>
                        <p>Je CSV bestand moet de volgende kolommen bevatten voor <strong>alleen open vragen</strong>:</p>
                        
                        <div class="bg-success bg-opacity-10 p-3 rounded border border-success">
                            <h6 class="text-success">Open Vragen Formaat:</h6>
                            <code>vraag_id;vraag;juiste_antwoord;punten</code>
                            
                            <div class="bg-light p-2 rounded mt-2">
                                <small><code>
                                    vraag_id;vraag;juiste_antwoord;punten<br>
                                    Q001;Wat is de hoofdstad van Frankrijk?;Parijs;1<br>
                                    Q002;Hoeveel benen heeft een spin?;8;1<br>
                                    Q003;In welk jaar begon de Tweede Wereldoorlog?;1939;2<br>
                                    Q004;Wat is de kleur van de zon?;geel;1
                                </code></small>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <strong>Belangrijk voor Open Vragen:</strong>
                            <ul class="mb-0">
                                <li><strong>Antwoorden:</strong> Case-insensitive (hoofdletters worden genegeerd)</li>
                                <li><strong>Spaties:</strong> Worden genegeerd bij controle</li>
                                <li><strong>Punten:</strong> Optioneel, standaard 1 punt per vraag</li>
                                <li><strong>Scheidingsteken:</strong> Puntkomma (;)</li>
                                <li><strong>Eerste rij:</strong> Kolomnamen</li>
                                <li><strong>Bestandsgrootte:</strong> Max 2MB</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Upload Form -->
                    <form action="{{ route('teacher.questions.store-open') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                        @csrf
                        <div class="mb-4">
                            <label for="csv_file" class="form-label">
                                <i class="fas fa-file-csv"></i> Kies je Open Vragen CSV bestand
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
                        </div>

                        <div class="mb-4">
                            <label for="quiz_title" class="form-label">
                                <i class="fas fa-heading"></i> Quiz Titel
                            </label>
                            <input type="text" 
                                   class="form-control @error('quiz_title') is-invalid @enderror" 
                                   id="quiz_title" 
                                   name="quiz_title" 
                                   placeholder="Bijv: Open Vragen Quiz - Geschiedenis"
                                   value="{{ old('quiz_title') }}"
                                   required>
                            @error('quiz_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="quiz_description" class="form-label">
                                <i class="fas fa-align-left"></i> Quiz Beschrijving (optioneel)
                            </label>
                            <textarea class="form-control" 
                                      id="quiz_description" 
                                      name="quiz_description" 
                                      rows="3"
                                      placeholder="Beschrijf waar deze open vragen quiz over gaat...">{{ old('quiz_description') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-success" onclick="downloadOpenTemplate()">
                                <i class="fas fa-download"></i> Download Template
                            </button>
                            
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-upload"></i> Upload Open Vragen
                            </button>
                        </div>
                    </form>

                    <!-- Help Section -->
                    <div class="mt-5 pt-4 border-top">
                        <h6><i class="fas fa-question-circle"></i> Tips voor Open Vragen:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="text-muted">
                                    <li>Houd antwoorden kort en duidelijk</li>
                                    <li>Vermijd synoniemen in het juiste antwoord</li>
                                    <li>Test je antwoorden vooraf</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="text-muted">
                                    <li>Gebruik getallen voor exacte waarden</li>
                                    <li>Eenduidige vragen stellen</li>
                                    <li>Consistente antwoordformaten</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function downloadOpenTemplate() {
    const csvContent = `vraag_id;vraag;juiste_antwoord;punten
Q001;Wat is de hoofdstad van Frankrijk?;Parijs;1
Q002;Hoeveel benen heeft een spin?;8;1
Q003;In welk jaar begon de Tweede Wereldoorlog?;1939;2
Q004;Wat is de kleur van de zon?;geel;1
Q005;Hoeveel continenten zijn er?;7;1
Q006;Wat is de chemische formule van water?;H2O;2`;
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'open_questions_template.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection
