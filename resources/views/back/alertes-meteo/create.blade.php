@extends('layouts.back')

@section('title', 'Nouvelle alerte météo')
@section('page-title', 'Nouvelle alerte météo')

@section('page-actions')
    <a href="{{ route('admin.alertes-meteo.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('admin.alertes-meteo.store') }}" novalidate>
            @csrf
            @include('back.alertes-meteo._form')
            <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Publier l'alerte</button>
        </form>
    </x-card>
@endsection
