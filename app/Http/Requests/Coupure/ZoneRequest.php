<?php

namespace App\Http\Requests\Coupure;

use App\Models\Zone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation de la création et de la modification d'une zone.
 */
class ZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nom' => trim((string) $this->nom),
        ]);
    }

    public function rules(): array
    {
        return [
            'nom' => [
                'required', 'string', 'min:2', 'max:100',
                'regex:/^[\pL\s\'\-]+$/u',
                Rule::unique('zones', 'nom')->ignore($this->route('zone')),
            ],
            'gouvernorat' => ['required', Rule::in(Zone::GOUVERNORATS)],
            'code_postal' => ['required', 'digits:4'],
            'population' => ['nullable', 'integer', 'min:0', 'max:5000000'],
            // Limites approximatives du territoire tunisien
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:30,37.6'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:7.5,11.6'],
            'niveau_risque' => ['required', Rule::in(array_keys(Zone::NIVEAUX_RISQUE))],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.unique' => 'Une zone porte déjà ce nom.',
            'nom.regex' => 'Le nom ne peut contenir que des lettres, espaces, apostrophes et tirets.',
            'code_postal.digits' => 'Le code postal tunisien doit contenir exactement 4 chiffres.',
            'gouvernorat.in' => 'Veuillez choisir un gouvernorat de la liste.',
            'latitude.between' => 'La latitude doit se situer en Tunisie (entre 30 et 37,6).',
            'longitude.between' => 'La longitude doit se situer en Tunisie (entre 7,5 et 11,6).',
        ];
    }

    public function attributes(): array
    {
        return [
            'nom' => 'nom de la zone',
            'code_postal' => 'code postal',
            'niveau_risque' => 'niveau de risque',
        ];
    }
}
