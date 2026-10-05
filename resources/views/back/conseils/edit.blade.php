@extends('layouts.back')

@section('title', 'Modifier – ' . $conseil->titre)
@section('page-title', '✏️ Modifier : ' . $conseil->titre)

@section('page-actions')
    <a href="{{ route('admin.conseils.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.conseils.update', $conseil) }}" method="POST">
            @csrf
            @method('PUT')
            @include('back.conseils._form')
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save"></i> Mettre à jour
                </button>
                <a href="{{ route('admin.conseils.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
