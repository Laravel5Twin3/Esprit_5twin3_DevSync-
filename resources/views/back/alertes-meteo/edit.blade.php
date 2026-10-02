@extends('layouts.back')

@section('title', 'Modifier l\'alerte')
@section('page-title', 'Modifier « '.$alerte->titre.' »')

@section('page-actions')
    <a href="{{ route('admin.alertes-meteo.show', $alerte) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('admin.alertes-meteo.update', $alerte) }}" novalidate>
            @csrf
            @method('PUT')
            @include('back.alertes-meteo._form')
            <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i> Mettre à jour</button>
        </form>
    </x-card>
@endsection
