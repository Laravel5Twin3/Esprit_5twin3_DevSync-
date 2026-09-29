@extends('layouts.back')

@section('title', 'Nouvelle catégorie')
@section('page-title', '➕ Nouvelle catégorie')

@section('page-actions')
    <a href="{{ route('admin.categories-point.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
@endsection

@section('content')
<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <form action="{{ route('admin.categories-point.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-12">
                    <x-form.input name="nom" label="Nom de la catégorie *"
                        placeholder="Ex: Parc ombragé" :value="old('nom')" />
                </div>

                <div class="col-md-6">
                    <label class="form-label">Icône Bootstrap Icons</label>
                    <div class="input-group">
                        <span class="input-group-text"><i id="icon-preview" class="bi bi-geo-alt"></i></span>
                        <input type="text" name="icone" class="form-control"
                               id="icone-input" placeholder="bi-geo-alt"
                               value="{{ old('icone', 'bi-geo-alt') }}"
                               @error('icone') is-invalid @enderror>
                    </div>
                    @error('icone')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Voir <a href="https://icons.getbootstrap.com" target="_blank">icons.getbootstrap.com</a>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Couleur</label>
                    <input type="color" name="couleur" class="form-control form-control-color"
                           value="{{ old('couleur', '#0d6efd') }}">
                    @error('couleur')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <x-form.textarea name="description" label="Description"
                        placeholder="Décrivez cette catégorie de points de fraîcheur…"
                        :value="old('description')" />
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Enregistrer
                </button>
                <a href="{{ route('admin.categories-point.index') }}" class="btn btn-outline-secondary">Annuler</a>
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
