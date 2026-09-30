@extends('layouts.back')

@section('title', 'Nouvelle zone')
@section('page-title', 'Nouvelle zone')

@section('page-actions')
    <a href="{{ route('admin.zones.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('admin.zones.store') }}" novalidate>
            @csrf
            @include('back.zones._form')
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Enregistrer</button>
        </form>
    </x-card>
@endsection
