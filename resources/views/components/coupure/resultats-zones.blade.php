{{-- Risque prédit pour chaque zone (une seule journée). Usage : <x-coupure.resultats-zones :resultats="$resultats" /> --}}
@props(['resultats'])

<div class="row g-2">
    @foreach ($resultats as $resultat)
        <div class="col-sm-6 col-lg-4">
            <div class="border rounded p-2 h-100 d-flex justify-content-between align-items-center border-{{ $resultat['niveau']['couleur'] }}">
                <div>
                    <strong>{{ $resultat['zone']->nom }}</strong>
                    <div class="small text-muted">
                        <i class="bi bi-thermometer-half"></i> {{ round($resultat['temperature']) }} °C ·
                        {{ $resultat['facteurs'][0]['label'] }}
                    </div>
                </div>
                <span class="badge bg-{{ $resultat['niveau']['couleur'] }} fs-6 ms-2">{{ round($resultat['probabilite'] * 100) }} %</span>
            </div>
        </div>
    @endforeach
</div>
