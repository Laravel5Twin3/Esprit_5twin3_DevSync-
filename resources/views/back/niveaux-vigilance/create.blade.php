@extends('layouts.back')

@section('title', 'Nouveau niveau de vigilance')
@section('page-title', 'Nouveau niveau de vigilance')

@section('page-actions')
    <a href="{{ route('admin.niveaux-vigilance.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('admin.niveaux-vigilance.store') }}" novalidate>
            @csrf
            @include('back.niveaux-vigilance._form')
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Enregistrer</button>
        </form>
    </x-card>
@endsection
