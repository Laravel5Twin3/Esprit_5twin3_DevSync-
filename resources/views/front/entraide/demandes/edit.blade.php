@extends('layouts.front')

@section('title', 'Modifier ma demande')

@section('hero')
    <section class="ha-hero text-white">
        <div class="container py-4">

            <h1 class="fw-bold">
                <i class="bi bi-pencil"></i>
                Modifier ma demande
            </h1>

        </div>
    </section>
@endsection

@section('content')

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <x-card>

                <form method="POST"
                      action="{{ route('help-requests.update', $helpRequest) }}">

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
                            value="{{ old('title', $helpRequest->title) }}"
                            class="form-control"
                            required
                        >

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

                            @foreach ([
                                'Courses',
                                'Transport',
                                'Bricolage',
                                'Informatique',
                                'Garde',
                                'Déménagement',
                                'Autre'
                            ] as $category)

                                <option
                                    value="{{ $category }}"
                                    @selected(old('category', $helpRequest->category) === $category)
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
                            class="form-control"
                            required
                        >{{ old('description', $helpRequest->description) }}</textarea>

                    </div>

                    <div class="mb-3">

                        <label for="location" class="form-label">
                            Zone / quartier
                        </label>

                        <input
                            type="text"
                            id="location"
                            name="location"
                            value="{{ old('location', $helpRequest->location) }}"
                            class="form-control"
                        >

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label for="needed_from" class="form-label">
                                Besoin à partir de
                            </label>

                            <input
                                type="datetime-local"
                                id="needed_from"
                                name="needed_from"
                                value="{{ old('needed_from', $helpRequest->needed_from?->format('Y-m-d\TH:i')) }}"
                                class="form-control"
                            >

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="needed_until" class="form-label">
                                Besoin jusqu'à
                            </label>

                            <input
                                type="datetime-local"
                                id="needed_until"
                                name="needed_until"
                                value="{{ old('needed_until', $helpRequest->needed_until?->format('Y-m-d\TH:i')) }}"
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

                            <option value="open"
                                @selected(old('status', $helpRequest->status) === 'open')>
                                Ouverte
                            </option>

                            <option value="in_progress"
                                @selected(old('status', $helpRequest->status) === 'in_progress')>
                                En cours
                            </option>

                            <option value="completed"
                                @selected(old('status', $helpRequest->status) === 'completed')>
                                Terminée
                            </option>

                            <option value="cancelled"
                                @selected(old('status', $helpRequest->status) === 'cancelled')>
                                Annulée
                            </option>

                        </select>

                    </div>

                    <div class="d-flex justify-content-between mt-4">

                        <a href="{{ route('help-requests.show', $helpRequest) }}"
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
