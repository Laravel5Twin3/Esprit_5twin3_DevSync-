<?php

namespace App\Http\Requests\AlerteMeteo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation commune à la création et à la modification d'un niveau de vigilance.
 */
class NiveauVigilanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'min:2', 'max:50',
                Rule::unique('niveau_vigilances', 'nom')->ignore($this->route('niveau'))],
            'couleur' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'temperature_min' => ['required', 'numeric', 'between:-10,60'],
            'temperature_max' => ['nullable', 'numeric', 'between:-10,60', 'gt:temperature_min'],
            'consigne' => ['required', 'string', 'min:10', 'max:1000'],
            'ordre' => ['required', 'integer', 'between:1,10'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du niveau est obligatoire.',
            'nom.unique' => 'Ce niveau de vigilance existe déjà.',
            'nom.min' => 'Le nom doit contenir au moins :min caractères.',
            'couleur.required' => 'Choisissez une couleur.',
            'couleur.regex' => 'La couleur doit être au format #RRGGBB.',
            'temperature_min.required' => 'Le seuil minimum est obligatoire.',
            'temperature_min.numeric' => 'Le seuil minimum doit être un nombre.',
            'temperature_min.between' => 'Le seuil doit être compris entre :min et :max °C.',
            'temperature_max.numeric' => 'Le seuil maximum doit être un nombre.',
            'temperature_max.between' => 'Le seuil doit être compris entre :min et :max °C.',
            'temperature_max.gt' => 'Le seuil maximum doit être supérieur au seuil minimum.',
            'consigne.required' => 'La consigne à donner aux habitants est obligatoire.',
            'consigne.min' => 'La consigne doit contenir au moins :min caractères.',
            'ordre.required' => "L'ordre de gravité est obligatoire.",
            'ordre.integer' => "L'ordre doit être un nombre entier.",
            'ordre.between' => "L'ordre doit être compris entre :min et :max.",
        ];
    }
}
