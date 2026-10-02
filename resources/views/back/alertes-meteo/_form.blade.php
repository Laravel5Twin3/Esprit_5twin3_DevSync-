{{-- Formulaire partagé par create et edit. Variables : $alerte, $zones, $niveaux --}}
<div class="row">
    <div class="col-md-8">
        <x-form.input name="titre" label="Titre de l'alerte" :value="$alerte->titre" required maxlength="150"
                      placeholder="Ex : Vague de chaleur sur le Grand Tunis" />
    </div>
    <div class="col-md-4">
        <x-form.select name="zone_id" label="Zone concernée" required :options="$zones" :value="$alerte->zone_id" />
    </div>

    <div class="col-md-4">
        <x-form.input name="temperature_max" type="number" step="0.1" label="Température maximale (°C)"
                      :value="$alerte->temperature_max" required min="20" max="60" placeholder="Ex : 42.5" />
    </div>
    <div class="col-md-8">
        {{-- Relation : liste déroulante des niveaux de vigilance --}}
        <x-form.select name="niveau_vigilance_id" label="Niveau de vigilance"
                       :options="$niveaux->mapWithKeys(fn ($n) => [$n->id => $n->nom.' ('.$n->plage.')'])"
                       :value="$alerte->niveau_vigilance_id"
                       placeholder="Automatique (selon la température)" />
        <div class="form-text mt-n2 mb-3" id="niveau-suggere"></div>
    </div>

    <div class="col-md-6">
        <x-form.input name="date_debut" type="datetime-local" label="Début" required
                      :value="$alerte->date_debut?->format('Y-m-d\TH:i')" />
    </div>
    <div class="col-md-6">
        <x-form.input name="date_fin" type="datetime-local" label="Fin" required
                      :value="$alerte->date_fin?->format('Y-m-d\TH:i')" />
    </div>

    <div class="col-12">
        <x-form.textarea name="message" label="Message aux habitants" :value="$alerte->message" rows="4" required maxlength="2000" />
    </div>
</div>

@push('scripts')
    <script>
        // Aperçu en direct du niveau qui sera choisi automatiquement
        (() => {
            const niveaux = @js($niveaux->map(fn ($n) => ['nom' => $n->nom, 'min' => $n->temperature_min])->sortByDesc('min')->values());
            const temp = document.getElementById('temperature_max');
            const select = document.getElementById('niveau_vigilance_id');
            const aide = document.getElementById('niveau-suggere');
            const maj = () => {
                const t = parseFloat(temp.value);
                if (select.value || isNaN(t)) { aide.textContent = ''; return; }
                const n = niveaux.find(n => t >= n.min) ?? niveaux[niveaux.length - 1];
                aide.innerHTML = n ? `<i class="bi bi-magic"></i> Niveau automatique : <strong>${n.nom}</strong>` : '';
            };
            temp.addEventListener('input', maj);
            select.addEventListener('change', maj);
            maj();
        })();
    </script>
@endpush
