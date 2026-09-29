@extends('layouts.front')

@section('title', 'Accueil')

@section('hero')
    <section class="ha-hero text-white">
        <div class="container py-5">
            <h1 class="display-5 fw-bold">Anticipez la canicule et les coupures de courant</h1>
            <p class="lead col-lg-8">
                HeatAlert centralise les alertes météo, les coupures d'électricité en cours ou prévues,
                les points de fraîcheur à proximité et des conseils adaptés pour votre quartier.
            </p>
            @guest
                <a href="{{ route('register') }}" class="btn btn-light btn-lg mt-2">Rejoindre HeatAlert</a>
            @endguest
        </div>
    </section>
@endsection

@section('content')
    <div class="row g-4">
        @foreach ([
            ['icon' => 'bi-cloud-sun', 'title' => 'Alertes météo', 'text' => 'Suivez les épisodes de canicule annoncés pour votre zone.'],
            ['icon' => 'bi-lightning-charge', 'title' => 'Coupures d\'électricité', 'text' => 'Consultez et signalez les délestages et surcharges du réseau.'],
            ['icon' => 'bi-tree', 'title' => 'Points de fraîcheur', 'text' => 'Parcs, salles climatisées et fontaines accessibles à proximité.'],
            ['icon' => 'bi-lightbulb', 'title' => 'Conseils', 'text' => 'Hydratation, économie d\'énergie, protection des équipements.'],
        ] as $feature)
            <div class="col-md-6 col-lg-3">
                <x-card class="h-100 text-center">
                    <i class="bi {{ $feature['icon'] }} ha-feature-icon"></i>
                    <h5 class="mt-3">{{ $feature['title'] }}</h5>
                    <p class="text-muted mb-0">{{ $feature['text'] }}</p>
                </x-card>
            </div>
        @endforeach
    </div>
@endsection
