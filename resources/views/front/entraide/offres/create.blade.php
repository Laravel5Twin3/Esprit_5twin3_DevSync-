@extends('layouts.front')

@section('title', 'Proposer mon aide')

@section('hero')
    <section class="ha-hero text-white">
        <div class="container py-4">
            <h1 class="fw-bold">
                <i class="bi bi-hand-thumbs-up"></i>
                Proposer mon aide
            </h1>

            <p class="lead mb-0">
                Partagez votre temps et vos compétences avec vos voisins.
            </p>
        </div>
    </section>
@endsection

@section('content')

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <x-card>

                <form method="POST"
                      action="{{ route('help-offers.store') }}">

                    @csrf

                    <div class="mb-3">

                        <label for="title" class="form-label">
                            Titre de l'offre *
                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title') }}"
                            class="form-control @error('title') is-invalid @enderror"
                            placeholder="Ex : Je peux aider pour faire les courses"
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
                            class="form-select @error('category') is-invalid @enderror"
                        >
                            <option value="">Choisir une catégorie</option>
                            <option value="Courses" @selected(old('category') === 'Courses')>
                                Courses
                            </option>
                            <option value="Transport" @selected(old('category') === 'Transport')>
                                Transport
                            </option>
                            <option value="Bricolage" @selected(old('category') === 'Bricolage')>
                                Bricolage
                            </option>
                            <option value="Informatique" @selected(old('category') === 'Informatique')>
                                Informatique
                            </option>
                            <option value="Garde" @selected(old('category') === 'Garde')>
                                Garde
                            </option>
                            <option value="Autre" @selected(old('category') === 'Autre')>
                                Autre
                            </option>
                        </select>

                        @error('category')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

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
                            placeholder="Décrivez ce que vous pouvez proposer..."
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
                            class="form-control @error('location') is-invalid @enderror"
                            placeholder="Ex : Centre-ville"
                        >

                        @error('location')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

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
                                value="{{ old('available_from') }}"
                                class="form-control @error('available_from') is-invalid @enderror"
                            >

                            @error('available_from')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="available_until" class="form-label">
                                Disponible jusqu'à
                            </label>

                            <input
                                type="datetime-local"
                                id="available_until"
                                name="available_until"
                                value="{{ old('available_until') }}"
                                class="form-control @error('available_until') is-invalid @enderror"
                            >

                            @error('available_until')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                    <div class="d-flex justify-content-between mt-4">

                        <a href="{{ route('help-offers.index') }}"
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i>
                            Annuler
                        </a>

                        <button type="submit"
                                class="btn btn-primary">
                            <i class="bi bi-check-circle"></i>
                            Publier mon offre
                        </button>

                    </div>

                </form>

            </x-card>

        </div>

    </div>

@endsection
