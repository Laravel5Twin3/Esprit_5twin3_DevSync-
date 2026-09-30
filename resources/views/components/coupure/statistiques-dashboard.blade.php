<h2 class="h5 mb-3"><i class="bi bi-lightning-charge"></i> Coupures de courant</h2>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.coupures.index', ['statut' => 'en_cours']) }}" class="text-decoration-none">
            <x-back.stat-card icon="bi-exclamation-triangle" label="Coupures en cours" :value="$enCours" color="danger" />
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.coupures.index', ['statut' => 'prevue']) }}" class="text-decoration-none">
            <x-back.stat-card icon="bi-calendar-event" label="Prévues (7 jours)" :value="$prevues" color="warning" />
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <x-back.stat-card icon="bi-house" label="Foyers sans courant" :value="number_format($foyers, 0, ',', ' ')" color="info" />
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.signalements.index') }}" class="text-decoration-none">
            <x-back.stat-card icon="bi-megaphone" label="Signalements à vérifier" :value="$signalementsEnAttente"
                              :color="$signalementsEnAttente ? 'warning' : 'secondary'" />
        </a>
    </div>
</div>

@if ($risqueDemain)
    <div class="alert alert-{{ $risqueDemain['niveau']['couleur'] }} d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <i class="bi bi-cpu"></i>
            <strong>Prédiction pour demain :</strong> zone la plus exposée <strong>{{ $risqueDemain['zone']->nom }}</strong>
            — risque {{ strtolower($risqueDemain['niveau']['label']) }} ({{ round($risqueDemain['probabilite'] * 100) }} %,
            {{ round($risqueDemain['temperature']) }} °C prévus).
        </div>
        <a href="{{ route('admin.coupures-ia.index') }}" class="btn btn-sm btn-light">Voir les prédictions</a>
    </div>
@endif
