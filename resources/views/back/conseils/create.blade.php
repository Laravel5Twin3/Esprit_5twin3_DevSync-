@extends('layouts.back')

@section('title', 'Ajouter un conseil')
@section('page-title', '➕ Nouveau conseil')

@section('page-actions')
    <a href="{{ route('admin.conseils.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.conseils.store') }}" method="POST">
            @csrf
            @include('back.conseils._form')
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Enregistrer
                </button>
                <a href="{{ route('admin.conseils.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
