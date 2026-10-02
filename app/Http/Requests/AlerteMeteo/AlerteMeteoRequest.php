<?php

namespace App\Http\Requests\AlerteMeteo;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation commune à la création et à la modification d'une alerte météo.
 * Le niveau est facultatif : s'il est vide, il est calculé selon la température.
 */
class AlerteMeteoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'min:3', 'max:150'],
            'zone_id' => ['required', 'exists:zones,id'],
            'niveau_vigilance_id' => ['nullable', 'exists:niveau_vigilances,id'],
            'temperature_max' => ['required', 'numeric', 'between:20,60'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after:date_debut'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre de l\'alerte est obligatoire.',
            'titre.min' => 'Le titre doit contenir au moins :min caractères.',
            'titre.max' => 'Le titre ne doit pas dépasser :max caractères.',
            'zone_id.required' => 'Veuillez choisir la zone concernée.',
            'zone_id.exists' => 'La zone sélectionnée est invalide.',
            'niveau_vigilance_id.exists' => 'Le niveau sélectionné est invalide.',
            'temperature_max.required' => 'La température maximale est obligatoire.',
            'temperature_max.numeric' => 'La température doit être un nombre.',
            'temperature_max.between' => 'La température doit être comprise entre :min et :max °C.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_debut.date' => 'La date de début est invalide.',
            'date_fin.required' => 'La date de fin est obligatoire.',
            'date_fin.date' => 'La date de fin est invalide.',
            'date_fin.after' => 'La date de fin doit être postérieure à la date de début.',
            'message.required' => 'Le message aux habitants est obligatoire.',
            'message.min' => 'Le message doit contenir au moins :min caractères.',
        ];
    }
}
