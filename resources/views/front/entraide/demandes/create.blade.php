@extends('layouts.front')

@section('title', 'Demander de l\'aide')

@section('hero')
    <section class="ha-hero text-white">
        <div class="container py-4">

            <h1 class="fw-bold">
                <i class="bi bi-hand-index-thumb"></i>
                Demander de l'aide
            </h1>

            <p class="lead mb-0">
                Expliquez votre besoin et laissez vos voisins vous proposer leur aide.
            </p>

        </div>
    </section>
@endsection

@section('content')

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <x-card>

                <form method="POST"
                      action="{{ route('help-requests.store') }}">

                    @csrf

                    <div class="mb-3">

                        <label for="title" class="form-label">
                            Titre de la demande *
                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title') }}"
                            class="form-control @error('title') is-invalid @enderror"
                            placeholder="Ex : Besoin d'aide pour déménager"
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
                            <option value="">Choisir une catégorie</option>

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
                                    @selected(old('category') === $category)
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
                            placeholder="Expliquez précisément ce dont vous avez besoin..."
                            required
                        >{{ old('description') }}</textarea>

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
                            value="{{ old('location') }}"
                            class="form-control"
                            placeholder="Ex : Centre-ville"
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
                                value="{{ old('needed_from') }}"
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
                                value="{{ old('needed_until') }}"
                                class="form-control"
                            >

                        </div>

                    </div>

                    <div class="d-flex justify-content-between mt-4">

                        <a href="{{ route('help-requests.index') }}"
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i>
                            Annuler
                        </a>

                        <button type="submit"
                                class="btn btn-primary">
                            <i class="bi bi-send"></i>
                            Publier la demande
                        </button>

                    </div>

                </form>

            </x-card>

        </div>

    </div>

@endsection
