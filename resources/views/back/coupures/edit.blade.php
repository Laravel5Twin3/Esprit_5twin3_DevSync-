@extends('layouts.back')

@section('title', 'Modifier la coupure')
@section('page-title', 'Modifier la coupure #'.$coupure->id)

@section('page-actions')
    <a href="{{ route('admin.coupures.show', $coupure) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('admin.coupures.update', $coupure) }}" novalidate>
            @csrf
            @method('PUT')
            @include('back.coupures._form')
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Mettre à jour</button>
        </form>
    </x-card>
@endsection
