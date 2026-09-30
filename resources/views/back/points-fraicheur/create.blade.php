@extends('layouts.back')

@section('title', 'Ajouter un point de fraîcheur')
@section('page-title', '➕ Nouveau point de fraîcheur')

@section('page-actions')
    <a href="{{ route('admin.points-fraicheur.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.points-fraicheur.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                {{-- Catégorie --}}
                <div class="col-md-6">
                    <x-form.select
                        name="categorie_point_id"
                        label="Catégorie *"
                        :options="$categories->pluck('nom', 'id')"
                        :selected="old('categorie_point_id')"
                        placeholder="— Sélectionner une catégorie —"
                    />
                </div>

                {{-- Nom --}}
                <div class="col-md-6">
                    <x-form.input
                        name="nom"
                        label="Nom du point *"
                        placeholder="Ex: Parc du Belvédère"
                        :value="old('nom')"
                    />
                </div>

                {{-- Adresse --}}
                <div class="col-12">
                    <x-form.input
                        name="adresse"
                        label="Adresse *"
                        placeholder="Ex: Avenue Farhat Hached, Tunis"
                        :value="old('adresse')"
                    />
                </div>

                {{-- Latitude / Longitude --}}
                <div class="col-md-6">
                    <x-form.input
                        name="latitude"
                        label="Latitude *"
                        type="number"
                        step="0.0000001"
                        placeholder="Ex: 36.8190"
                        :value="old('latitude')"
                    />
                </div>
                <div class="col-md-6">
                    <x-form.input
                        name="longitude"
                        label="Longitude *"
                        type="number"
                        step="0.0000001"
                        placeholder="Ex: 10.1657"
                        :value="old('longitude')"
                    />
                </div>

                {{-- Mini-carte pour choisir les coordonnées --}}
                <div class="col-12">
                    <label class="form-label text-muted small">
                        <i class="bi bi-map"></i> Cliquer sur la carte pour remplir les coordonnées
                    </label>
                    <div id="pick-map" style="height: 300px; border-radius: 8px; border: 1px solid #dee2e6;"></div>
                </div>

                {{-- Horaires --}}
                <div class="col-md-6">
                    <x-form.input
                        name="horaires"
                        label="Horaires"
                        placeholder="Ex: 08h00 - 20h00 ou 24h/24"
                        :value="old('horaires')"
                    />
                </div>

                {{-- Capacité --}}
                <div class="col-md-6">
                    <x-form.input
                        name="capacite"
                        label="Capacité (personnes)"
                        type="number"
                        min="1"
                        placeholder="Ex: 500"
                        :value="old('capacite')"
                    />
                </div>

                {{-- Description --}}
                <div class="col-12">
                    <x-form.textarea
                        name="description"
                        label="Description"
                        placeholder="Informations utiles sur ce point de fraîcheur…"
                        :value="old('description')"
                    />
                </div>

                {{-- Actif --}}
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="actif" name="actif"
                               value="1" {{ old('actif', 1) ? 'checked' : '' }}>
                        <label class="form-check-label" for="actif">Point actif (visible sur la carte)</label>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Enregistrer
                </button>
                <a href="{{ route('admin.points-fraicheur.index') }}" class="btn btn-outline-secondary">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('pick-map').setView([36.8190, 10.1657], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let marker = null;

    // Si des valeurs existent déjà (old())
    const initLat = parseFloat('{{ old("latitude", "") }}');
    const initLng = parseFloat('{{ old("longitude", "") }}');
    if (!isNaN(initLat) && !isNaN(initLng)) {
        marker = L.marker([initLat, initLng]).addTo(map);
        map.setView([initLat, initLng], 15);
    }

    map.on('click', function(e) {
        const { lat, lng } = e.latlng;
        if (marker) marker.remove();
        marker = L.marker([lat, lng]).addTo(map);
        document.querySelector('[name="latitude"]').value = lat.toFixed(7);
        document.querySelector('[name="longitude"]').value = lng.toFixed(7);
    });
</script>
@endpush
