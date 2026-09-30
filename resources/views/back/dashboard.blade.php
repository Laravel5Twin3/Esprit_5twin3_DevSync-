@extends('layouts.back')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <x-back.stat-card icon="bi-people" label="Utilisateurs" :value="$usersCount" color="primary" />
        </div>
    </div>

    {{-- Blocs des modules : un fichier par module dans resources/views/back/dashboard/ (chargés automatiquement) --}}
    @foreach (glob(resource_path('views/back/dashboard/*.blade.php')) as $widget)
        @include('back.dashboard.'.basename($widget, '.blade.php'))
    @endforeach
@endsection
