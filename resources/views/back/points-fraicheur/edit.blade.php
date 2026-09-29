@extends('layouts.back')

@section('title', 'Modifier – ' . $point->nom)
@section('page-title', '✏️ Modifier : ' . $point->nom)

@section('page-actions')
    <a href="{{ route('admin.points-fraicheur.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.points-fraicheur.update', $point) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <x-form.select
                        name="categorie_point_id"
                        label="Catégorie *"
                        :options="$categories->pluck('nom', 'id')"
                        :selected="old('categorie_point_id', $point->categorie_point_id)"
                        placeholder="— Sélectionner une catégorie —"
                    />
                </div>

                <div class="col-md-6">
                    <x-form.input
                        name="nom"
                        label="Nom du point *"
                        :value="old('nom', $point->nom)"
                    />
                </div>

                <div class="col-12">
                    <x-form.input
                        name="adresse"
                        label="Adresse *"
                        :value="old('adresse', $point->adresse)"
                    />
                </div>

                <div class="col-md-6">
                    <x-form.input
                        name="latitude"
                        label="Latitude *"
                        type="number"
                        step="0.0000001"
                        :value="old('latitude', $point->latitude)"
                    />
                </div>
                <div class="col-md-6">
                    <x-form.input
                        name="longitude"
                        label="Longitude *"
                        type="number"
                        step="0.0000001"
                        :value="old('longitude', $point->longitude)"
                    />
                </div>

                {{-- Mini-carte pré-positionnée sur le point existant --}}
                <div class="col-12">
                    <label class="form-label text-muted small">
                        <i class="bi bi-map"></i> Cliquer pour repositionner le point
                    </label>
                    <div id="pick-map" style="height: 280px; border-radius: 8px; border: 1px solid #dee2e6;"></div>
                </div>

                <div class="col-md-6">
                    <x-form.input
                        name="horaires"
                        label="Horaires"
                        :value="old('horaires', $point->horaires)"
                    />
                </div>

                <div class="col-md-6">
                    <x-form.input
                        name="capacite"
                        label="Capacité (personnes)"
                        type="number"
                        min="1"
                        :value="old('capacite', $point->capacite)"
                    />
                </div>

                <div class="col-12">
                    <x-form.textarea
                        name="description"
                        label="Description"
                        :value="old('description', $point->description)"
                    />
                </div>

                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="actif" name="actif"
                               value="1" {{ old('actif', $point->actif) ? 'checked' : '' }}>
                        <label class="form-check-label" for="actif">Point actif</label>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save"></i> Mettre à jour
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
    const initLat = {{ $point->latitude }};
    const initLng = {{ $point->longitude }};

    const map = L.map('pick-map').setView([initLat, initLng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let marker = L.marker([initLat, initLng]).addTo(map);

    map.on('click', function(e) {
        const { lat, lng } = e.latlng;
        marker.setLatLng([lat, lng]);
        document.querySelector('[name="latitude"]').value = lat.toFixed(7);
        document.querySelector('[name="longitude"]').value = lng.toFixed(7);
    });
</script>
@endpush
