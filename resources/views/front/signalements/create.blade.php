@extends('layouts.front')

@section('title', 'Signaler une coupure')

@section('content')
    <nav class="mb-3">
        <a href="{{ route('coupures.index') }}" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Coupures</a>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <x-card title="Signaler une coupure de courant">
                <p class="text-muted">
                    Vous constatez une coupure qui n'apparaît pas dans la liste ? Prévenez-nous : un administrateur
                    vérifiera votre signalement et informera les habitants de la zone.
                </p>

                <form method="POST" action="{{ route('signalements.store') }}" novalidate>
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <x-form.select name="zone_id" label="Votre zone" required :options="$zones" :value="$zoneId"
                                           placeholder="-- Choisir votre zone --" />
                        </div>
                        <div class="col-md-6">
                            <x-form.input name="date_constat" type="datetime-local" label="Depuis quand ?" required
                                          :value="now()->format('Y-m-d\TH:i')"
                                          :max="now()->format('Y-m-d\TH:i')"
                                          help="Dans les dernières 24 heures." />
                        </div>
                        <div class="col-12">
                            <x-form.textarea name="description" label="Que constatez-vous ?" required rows="4" maxlength="1000"
                                             placeholder="Ex : plus de courant dans tout l'immeuble, les voisins sont aussi touchés." />
                        </div>
                    </div>

                    <button type="submit" class="btn btn-ha"><i class="bi bi-send"></i> Envoyer le signalement</button>
                </form>
            </x-card>
        </div>
    </div>
@endsection
