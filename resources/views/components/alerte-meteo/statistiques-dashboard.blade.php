<h2 class="h5 mb-3"><i class="bi bi-thermometer-sun"></i> Alertes météo / canicule</h2>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.alertes-meteo.index', ['statut' => 'en_cours']) }}" class="text-decoration-none">
            <x-back.stat-card icon="bi-exclamation-triangle" label="Alertes en cours" :value="$enCours" color="danger" />
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.alertes-meteo.index', ['statut' => 'a_venir']) }}" class="text-decoration-none">
            <x-back.stat-card icon="bi-calendar-event" label="Alertes à venir" :value="$aVenir" color="info" />
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <x-back.stat-card icon="bi-thermometer-high" label="Température max en cours"
                          :value="$temperatureMax ? $temperatureMax.' °C' : '—'" color="warning" />
    </div>
    <div class="col-sm-6 col-xl-3">
        <x-back.stat-card icon="bi-geo-alt" label="Zones sous alerte" :value="$zonesTouchees" color="primary" />
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white fw-semibold">Alertes actives par niveau de vigilance</div>
    <div class="card-body">
        @php $total = max(1, $parNiveau->sum('alertes_count')); @endphp
        @foreach ($parNiveau as $niveau)
            <div class="d-flex align-items-center gap-2 mb-2">
                <div style="width: 6rem"><x-alerte-meteo.badge-niveau :niveau="$niveau" /></div>
                <div class="progress flex-grow-1" style="height: 1.1rem" role="progressbar"
                     aria-label="{{ $niveau->nom }}" aria-valuenow="{{ $niveau->alertes_count }}" aria-valuemin="0" aria-valuemax="{{ $total }}">
                    <div class="progress-bar" style="width: {{ $niveau->alertes_count / $total * 100 }}%; background: {{ $niveau->couleur }}"></div>
                </div>
                <span class="fw-semibold" style="width: 2rem">{{ $niveau->alertes_count }}</span>
            </div>
        @endforeach
    </div>
</div>
