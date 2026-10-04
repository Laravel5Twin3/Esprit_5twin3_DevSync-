@extends('layouts.back')

@section('title', 'Détail de l’offre d’aide')

@section('page-title')
    <i class="bi bi-hand-thumbs-up me-2"></i>
    Détail de l’offre d’aide
@endsection

@section('page-actions')
    <a
        href="{{ route('admin.entraide.index') }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left me-1"></i>
        Retour à l’entraide
    </a>
@endsection

@section('content')

    <div class="row g-4">

        {{-- ===================================================== --}}
        {{-- INFORMATIONS DE L'OFFRE                               --}}
        {{-- ===================================================== --}}

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <h5 class="mb-0">
                                <i class="bi bi-hand-thumbs-up text-success me-2"></i>
                                Offre d’aide
                            </h5>

                            <small class="text-muted">
                                Informations publiées par le voisin
                            </small>
                        </div>

                        <span class="badge bg-success">
                            Offre disponible
                        </span>

                    </div>

                </div>


                <div class="card-body">

                    {{-- Titre --}}
                    <div class="mb-4">

                        <label class="form-label text-muted small mb-1">
                            Titre
                        </label>

                        <h3 class="mb-0">
                            {{ $helpOffer->title ?? $helpOffer->titre ?? 'Sans titre' }}
                        </h3>

                    </div>


                    {{-- Description --}}
                    <div class="mb-4">

                        <label class="form-label text-muted small mb-1">
                            Description
                        </label>

                        @if (!empty($helpOffer->description))

                            <div class="bg-light rounded p-3">
                                {!! nl2br(e($helpOffer->description)) !!}
                            </div>

                        @else

                            <div class="text-muted fst-italic">
                                Aucune description n’a été fournie.
                            </div>

                        @endif

                    </div>


                    {{-- Informations complémentaires --}}
                    <div class="row g-3">

                        <div class="col-md-6">

                            <div class="border rounded p-3 h-100">

                                <small class="text-muted d-block mb-1">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    Date de publication
                                </small>

                                <strong>
                                    {{ $helpOffer->created_at->format('d/m/Y') }}
                                </strong>

                                <br>

                                <small class="text-muted">
                                    à {{ $helpOffer->created_at->format('H:i') }}
                                </small>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="border rounded p-3 h-100">

                                <small class="text-muted d-block mb-1">
                                    <i class="bi bi-clock-history me-1"></i>
                                    Dernière modification
                                </small>

                                <strong>
                                    {{ $helpOffer->updated_at->format('d/m/Y') }}
                                </strong>

                                <br>

                                <small class="text-muted">
                                    à {{ $helpOffer->updated_at->format('H:i') }}
                                </small>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- ZONE DE SUPPRESSION                                --}}
            {{-- ================================================= --}}

            <div class="card border-0 shadow-sm mt-4">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h6 class="text-danger mb-1">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Zone dangereuse
                            </h6>

                            <p class="text-muted small mb-0">
                                La suppression de cette offre est définitive.
                            </p>

                        </div>

                        <form
                            action="{{ route('admin.entraide.offers.destroy', $helpOffer) }}"
                            method="POST"
                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ? Cette action est définitive.');"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn btn-danger"
                            >
                                <i class="bi bi-trash me-1"></i>
                                Supprimer l’offre
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- INFORMATIONS UTILISATEUR                              --}}
        {{-- ===================================================== --}}

        <div class="col-lg-4">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">

                    <h5 class="mb-0">
                        <i class="bi bi-person text-primary me-2"></i>
                        Proposée par
                    </h5>

                </div>


                <div class="card-body">

                    @if ($helpOffer->user)

                        <div class="text-center">

                            {{-- Avatar --}}
                            <div
                                class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 80px; height: 80px;"
                            >
                                <i class="bi bi-person fs-1 text-primary"></i>
                            </div>


                            {{-- Nom --}}
                            <h5 class="mb-1">
                                {{ $helpOffer->user->name }}
                            </h5>


                            {{-- Email --}}
                            <p class="text-muted mb-3">
                                {{ $helpOffer->user->email }}
                            </p>

                        </div>


                        <hr>


                        <div class="mb-3">

                            <small class="text-muted d-block">
                                <i class="bi bi-person-badge me-1"></i>
                                Identifiant utilisateur
                            </small>

                            <strong>
                                #{{ $helpOffer->user->id }}
                            </strong>

                        </div>


                        @if (!empty($helpOffer->user->phone))

                            <div class="mb-3">

                                <small class="text-muted d-block">
                                    <i class="bi bi-telephone me-1"></i>
                                    Téléphone
                                </small>

                                <strong>
                                    {{ $helpOffer->user->phone }}
                                </strong>

                            </div>

                        @endif


                        <div>

                            <small class="text-muted d-block">
                                <i class="bi bi-calendar-check me-1"></i>
                                Membre depuis
                            </small>

                            <strong>
                                {{ $helpOffer->user->created_at->format('d/m/Y') }}
                            </strong>

                        </div>

                    @else

                        <div class="text-center py-4">

                            <i class="bi bi-person-x display-5 text-muted"></i>

                            <p class="text-muted mt-3 mb-0">
                                L’utilisateur ayant créé cette offre
                                n’existe plus.
                            </p>

                        </div>

                    @endif

                </div>

            </div>


            {{-- ID de l'offre --}}
            <div class="card border-0 shadow-sm mt-4">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <span class="text-muted">
                            ID de l’offre
                        </span>

                        <strong>
                            #{{ $helpOffer->id }}
                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
