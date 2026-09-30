{{-- Formulaire partagé par create et edit. Variables attendues : $coupure, $zones --}}
<div class="row">
    <div class="col-md-8">
        <x-form.input name="titre" label="Titre" :value="$coupure->titre" required maxlength="150"
                      placeholder="Ex : Délestage programmé pic de chaleur" />
    </div>
    <div class="col-md-4">
        <x-form.select name="zone_id" label="Zone" required :options="$zones" :value="$coupure->zone_id"
                       placeholder="-- Choisir une zone --" />
    </div>

    <div class="col-md-6">
        <x-form.select name="type" label="Type" required :options="App\Models\Coupure::TYPES" :value="$coupure->type" />
    </div>
    <div class="col-md-6">
        <x-form.select name="statut" label="Statut" required :options="App\Models\Coupure::STATUTS"
                       :value="$coupure->statut" :placeholder="null" />
    </div>

    <div class="col-md-4">
        <x-form.input name="date_debut" type="datetime-local" label="Début" required
                      :value="$coupure->date_debut?->format('Y-m-d\TH:i')" />
    </div>
    <div class="col-md-4">
        <x-form.input name="date_fin" type="datetime-local" label="Fin (prévue ou réelle)"
                      :value="$coupure->date_fin?->format('Y-m-d\TH:i')" help="Obligatoire si la coupure est résolue." />
    </div>
    <div class="col-md-4">
        <x-form.input name="foyers_touches" type="number" label="Foyers touchés" :value="$coupure->foyers_touches" min="1" />
    </div>

    <div class="col-12">
        <x-form.textarea name="description" label="Description / cause" :value="$coupure->description" rows="4" maxlength="2000" />
    </div>
</div>

<div class="alert alert-light border small">
    <i class="bi bi-info-circle"></i>
    <strong>Prévue</strong> : le début doit être dans le futur ·
    <strong>En cours</strong> : le début doit être passé ·
    <strong>Résolue</strong> : début et fin dans le passé, fin obligatoire.
</div>
