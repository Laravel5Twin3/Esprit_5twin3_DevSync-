<h2 class="h5 mb-3"><i class="bi bi-lightbulb"></i> Conseils et prévention</h2>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.conseils.index') }}" class="text-decoration-none">
            <x-back.stat-card icon="bi-journal-text" label="Conseils" :value="$total" color="primary" />
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <x-back.stat-card icon="bi-check-circle" label="Conseils actifs" :value="$actifs" color="success" />
    </div>
    <div class="col-sm-6 col-xl-3">
        <x-back.stat-card icon="bi-exclamation-triangle" label="Urgents (actifs)" :value="$urgents" color="danger" />
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.categories-conseil.index') }}" class="text-decoration-none">
            <x-back.stat-card icon="bi-tags" label="Catégories" :value="$categories" color="warning" />
        </a>
    </div>
</div>
