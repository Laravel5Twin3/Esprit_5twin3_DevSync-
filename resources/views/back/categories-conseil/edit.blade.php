@extends('layouts.back')

@section('title', 'Modifier – ' . $categorie->nom)
@section('page-title', '✏️ Modifier : ' . $categorie->nom)

@section('page-actions')
    <a href="{{ route('admin.categories-conseil.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
@endsection

@section('content')
<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <form action="{{ route('admin.categories-conseil.update', $categorie) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-12">
                    <x-form.input name="nom" label="Nom de la catégorie *"
                        :value="old('nom', $categorie->nom)" />
                </div>

                <div class="col-md-6">
                    <label class="form-label">Icône Bootstrap Icons</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i id="icon-preview" class="bi {{ old('icone', $categorie->icone) }}"></i>
                        </span>
                        <input type="text" name="icone" class="form-control" id="icone-input"
                               value="{{ old('icone', $categorie->icone) }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Couleur</label>
                    <input type="color" name="couleur" class="form-control form-control-color"
                           value="{{ old('couleur', $categorie->couleur) }}">
                </div>

                <div class="col-12">
                    <x-form.textarea name="description" label="Description"
                        :value="old('description', $categorie->description)" />
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save"></i> Mettre à jour
                </button>
                <a href="{{ route('admin.categories-conseil.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('icone-input').addEventListener('input', function() {
        document.getElementById('icon-preview').className = 'bi ' + this.value;
    });
</script>
@endpush
