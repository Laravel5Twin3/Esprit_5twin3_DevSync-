@extends('layouts.front')

@section('title', 'Modifier mon offre')

@section('hero')
    <section class="ha-hero text-white">
        <div class="container py-4">
            <h1 class="fw-bold">
                <i class="bi bi-pencil"></i>
                Modifier mon offre
            </h1>
        </div>
    </section>
@endsection

@section('content')

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <x-card>

                <form method="POST"
                      action="{{ route('help-offers.update', $helpOffer) }}">

                    @csrf
                    @method('PUT')

                    <div class="mb-3">

                        <label for="title" class="form-label">
                            Titre *
                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title', $helpOffer->title) }}"
                            class="form-control @error('title') is-invalid @enderror"
                            required
                        >

                        @error('title')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="mb-3">

                        <label for="category" class="form-label">
                            Catégorie
                        </label>

                        <select
                            id="category"
                            name="category"
                            class="form-select"
                        >
                            @foreach (['Courses', 'Transport', 'Bricolage', 'Informatique', 'Garde', 'Autre'] as $category)

                                <option
                                    value="{{ $category }}"
                                    @selected(old('category', $helpOffer->category) === $category)
                                >
                                    {{ $category }}
                                </option>

                            @endforeach
                        </select>

                    </div>

                    <div class="mb-3">

                        <label for="description" class="form-label">
                            Description *
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            class="form-control @error('description') is-invalid @enderror"
                            required
                        >{{ old('description', $helpOffer->description) }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="mb-3">

                        <label for="location" class="form-label">
                            Zone / quartier
                        </label>

                        <input
                            type="text"
                            id="location"
                            name="location"
                            value="{{ old('location', $helpOffer->location) }}"
                            class="form-control"
                        >

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label for="available_from" class="form-label">
                                Disponible à partir de
                            </label>

                            <input
                                type="datetime-local"
                                id="available_from"
                                name="available_from"
                                value="{{ old('available_from', $helpOffer->available_from?->format('Y-m-d\TH:i')) }}"
                                class="form-control"
                            >

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="available_until" class="form-label">
                                Disponible jusqu'à
                            </label>

                            <input
                                type="datetime-local"
                                id="available_until"
                                name="available_until"
                                value="{{ old('available_until', $helpOffer->available_until?->format('Y-m-d\TH:i')) }}"
                                class="form-control"
                            >

                        </div>

                    </div>

                    <div class="mb-3">

                        <label for="status" class="form-label">
                            Statut
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="form-select"
                        >
                            <option value="active"
                                @selected(old('status', $helpOffer->status) === 'active')>
                                Active
                            </option>

                            <option value="completed"
                                @selected(old('status', $helpOffer->status) === 'completed')>
                                Terminée
                            </option>

                            <option value="cancelled"
                                @selected(old('status', $helpOffer->status) === 'cancelled')>
                                Annulée
                            </option>
                        </select>

                    </div>

                    <div class="d-flex justify-content-between mt-4">

                        <a href="{{ route('help-offers.show', $helpOffer) }}"
                           class="btn btn-outline-secondary">
                            Annuler
                        </a>

                        <button type="submit"
                                class="btn btn-primary">
                            <i class="bi bi-save"></i>
                            Enregistrer
                        </button>

                    </div>

                </form>

            </x-card>

        </div>

    </div>

@endsection
