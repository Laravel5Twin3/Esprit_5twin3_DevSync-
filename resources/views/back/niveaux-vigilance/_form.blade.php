{{-- Formulaire partagé par create et edit. Variable attendue : $niveau --}}
<div class="row">
    <div class="col-md-5">
        <x-form.input name="nom" label="Nom du niveau" :value="$niveau->nom" required maxlength="50" placeholder="Ex : Orange" />
    </div>
    <div class="col-md-3">
        <x-form.input name="couleur" type="color" label="Couleur" :value="$niveau->couleur" required
                      class="form-control-color w-100" />
    </div>
    <div class="col-md-4">
        <x-form.input name="ordre" type="number" label="Ordre de gravité" :value="$niveau->ordre" required
                      min="1" max="10" help="1 = le moins grave." />
    </div>

    <div class="col-md-6">
        <x-form.input name="temperature_min" type="number" step="0.1" label="Seuil minimum (°C)"
                      :value="$niveau->temperature_min" required placeholder="Ex : 39" />
    </div>
    <div class="col-md-6">
        <x-form.input name="temperature_max" type="number" step="0.1" label="Seuil maximum (°C)"
                      :value="$niveau->temperature_max" placeholder="Ex : 43.9"
                      help="Laisser vide pour le niveau le plus élevé (pas de limite)." />
    </div>

    <div class="col-12">
        <x-form.textarea name="consigne" label="Consigne pour les habitants" :value="$niveau->consigne"
                         rows="3" required maxlength="1000" />
    </div>
</div>
