@extends('layouts.back')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
    <div class="row g-4">
        <div class="col-sm-6 col-xl-3">
            <x-back.stat-card icon="bi-people" label="Utilisateurs" :value="$usersCount" color="primary" />
        </div>
        {{-- Chaque module pourra ajouter ses statistiques ici --}}
    </div>
@endsection
