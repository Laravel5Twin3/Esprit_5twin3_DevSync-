@extends('layouts.back')

@section('title', 'Modifier la zone')
@section('page-title', 'Modifier « '.$zone->nom.' »')

@section('page-actions')
    <a href="{{ route('admin.zones.show', $zone) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('admin.zones.update', $zone) }}" novalidate>
            @csrf
            @method('PUT')
            @include('back.zones._form')
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Mettre à jour</button>
        </form>
    </x-card>
@endsection
