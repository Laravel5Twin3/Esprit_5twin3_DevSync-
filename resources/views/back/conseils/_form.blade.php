<div class="row g-3">
    <div class="col-md-6">
        <x-form.select
            name="categorie_conseil_id"
            label="Catégorie *"
            :options="$categories->pluck('nom', 'id')"
            :value="old('categorie_conseil_id', $conseil->categorie_conseil_id ?? null)"
            placeholder="— Sélectionner une catégorie —"
        />
    </div>

    <div class="col-md-6">
        <x-form.input
            name="titre"
            label="Titre *"
            placeholder="Ex: Boire régulièrement, avant d'avoir soif"
            :value="old('titre', $conseil->titre ?? null)"
        />
    </div>

    <div class="col-12">
        <x-form.input
            name="resume"
            label="Résumé (affiché sur les cartes)"
            placeholder="Une phrase courte pour le Front Office"
            :value="old('resume', $conseil->resume ?? null)"
        />
    </div>

    <div class="col-12">
        <x-form.textarea
            name="contenu"
            label="Contenu *"
            rows="6"
            placeholder="Rédigez le conseil complet…"
            :value="old('contenu', $conseil->contenu ?? null)"
        />
    </div>

    <div class="col-md-6">
        <x-form.select
            name="public_cible"
            label="Public concerné *"
            :options="\App\Models\Conseil::PUBLICS"
            :value="old('public_cible', $conseil->public_cible ?? 'tous')"
            placeholder="— Choisir —"
        />
    </div>

    <div class="col-md-6">
        <x-form.select
            name="priorite"
            label="Priorité *"
            :options="\App\Models\Conseil::PRIORITES"
            :value="old('priorite', $conseil->priorite ?? 'info')"
            placeholder="— Choisir —"
        />
    </div>

    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="actif" name="actif"
                   value="1" {{ old('actif', $conseil->actif ?? 1) ? 'checked' : '' }}>
            <label class="form-check-label" for="actif">Conseil actif (visible sur le site)</label>
        </div>
    </div>
</div>
