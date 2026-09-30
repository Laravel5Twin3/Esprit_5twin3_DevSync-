{{-- Formulaire partagé par create et edit. Variable attendue : $zone --}}
<div class="row">
    <div class="col-md-6">
        <x-form.input name="nom" label="Nom de la zone" :value="$zone->nom" required maxlength="100" placeholder="Ex : La Marsa" />
    </div>
    <div class="col-md-6">
        <x-form.select name="gouvernorat" label="Gouvernorat" required
                       :options="array_combine(App\Models\Zone::GOUVERNORATS, App\Models\Zone::GOUVERNORATS)"
                       :value="$zone->gouvernorat" />
    </div>

    <div class="col-md-4">
        <x-form.input name="code_postal" label="Code postal" :value="$zone->code_postal" required
                      maxlength="4" inputmode="numeric" placeholder="Ex : 2070" />
    </div>
    <div class="col-md-4">
        <x-form.input name="population" type="number" label="Population" :value="$zone->population"
                      min="0" help="Nombre d'habitants (optionnel)." />
    </div>
    <div class="col-md-4">
        <x-form.select name="niveau_risque" label="Niveau de risque de coupure" required
                       :options="App\Models\Zone::NIVEAUX_RISQUE" :value="$zone->niveau_risque" :placeholder="null" />
    </div>

    <div class="col-md-6">
        <x-form.input name="latitude" type="number" step="0.0000001" label="Latitude" :value="$zone->latitude"
                      placeholder="Ex : 36.8780" help="Utilisée pour la météo de la zone (prédiction des coupures)." />
    </div>
    <div class="col-md-6">
        <x-form.input name="longitude" type="number" step="0.0000001" label="Longitude" :value="$zone->longitude"
                      placeholder="Ex : 10.3247" help="Sans coordonnées, la météo de Tunis centre est utilisée." />
    </div>

    <div class="col-12">
        <x-form.textarea name="description" label="Description" :value="$zone->description" rows="3" maxlength="1000" />
    </div>
</div>
