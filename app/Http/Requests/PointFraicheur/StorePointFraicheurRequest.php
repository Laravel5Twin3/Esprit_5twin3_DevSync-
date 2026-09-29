<?php

namespace App\Http\Requests\PointFraicheur;

use Illuminate\Foundation\Http\FormRequest;

class StorePointFraicheurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categorie_point_id' => ['required', 'exists:categorie_points,id'],
            'nom'                => ['required', 'string', 'min:3', 'max:100'],
            'adresse'            => ['required', 'string', 'min:5', 'max:255'],
            'latitude'           => ['required', 'numeric', 'between:-90,90'],
            'longitude'          => ['required', 'numeric', 'between:-180,180'],
            'horaires'           => ['nullable', 'string', 'max:100'],
            'capacite'           => ['nullable', 'integer', 'min:1', 'max:100000'],
            'actif'              => ['nullable', 'boolean'],
            'description'        => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'categorie_point_id.required' => 'Veuillez sélectionner une catégorie.',
            'categorie_point_id.exists'   => 'La catégorie sélectionnée est invalide.',
            'nom.required'                => 'Le nom du point de fraîcheur est obligatoire.',
            'nom.min'                     => 'Le nom doit contenir au moins :min caractères.',
            'adresse.required'            => "L'adresse est obligatoire.",
            'latitude.required'           => 'La latitude est obligatoire.',
            'latitude.numeric'            => 'La latitude doit être un nombre.',
            'latitude.between'            => 'La latitude doit être comprise entre -90 et 90.',
            'longitude.required'          => 'La longitude est obligatoire.',
            'longitude.numeric'           => 'La longitude doit être un nombre.',
            'longitude.between'           => 'La longitude doit être comprise entre -180 et 180.',
            'capacite.integer'            => 'La capacité doit être un nombre entier.',
            'capacite.min'                => 'La capacité doit être au moins :min.',
        ];
    }
}
