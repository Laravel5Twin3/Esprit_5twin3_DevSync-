@extends('layouts.back')

@section('title', 'Modifier '.$niveau->nom)
@section('page-title', 'Modifier le niveau « '.$niveau->nom.' »')

@section('page-actions')
    <a href="{{ route('admin.niveaux-vigilance.show', $niveau) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('admin.niveaux-vigilance.update', $niveau) }}" novalidate>
            @csrf
            @method('PUT')
            @include('back.niveaux-vigilance._form')
            <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i> Mettre à jour</button>
        </form>
    </x-card>
@endsection
