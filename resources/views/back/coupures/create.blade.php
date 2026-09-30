@extends('layouts.back')

@section('title', 'Nouvelle coupure')
@section('page-title', 'Nouvelle coupure')

@section('page-actions')
    <a href="{{ route('admin.coupures.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
@endsection

@section('content')
    @if ($zones->isEmpty())
        <div class="alert alert-warning">
            Aucune zone n'existe encore. <a href="{{ route('admin.zones.create') }}">Créez d'abord une zone</a>.
        </div>
    @else
        <x-card>
            <form method="POST" action="{{ route('admin.coupures.store') }}" novalidate>
                @csrf
                @include('back.coupures._form')
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Enregistrer</button>
            </form>
        </x-card>
    @endif
@endsection
