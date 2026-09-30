<?php

namespace App\Http\Requests\Coupure;

use App\Models\Coupure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation de la création et de la modification d'une coupure.
 * Les règles sur les dates dépendent du statut choisi.
 */
class CoupureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $statut = $this->input('statut');

        return [
            'zone_id' => ['required', 'integer', 'exists:zones,id'],
            'titre' => ['required', 'string', 'min:5', 'max:150'],
            'type' => ['required', Rule::in(array_keys(Coupure::TYPES))],
            'statut' => ['required', Rule::in(array_keys(Coupure::STATUTS))],

            'date_debut' => array_merge(
                ['required', 'date'],
                match ($statut) {
                    'prevue' => ['after:now'],                 // une coupure prévue est dans le futur
                    'en_cours', 'resolue' => ['before_or_equal:now'], // déjà commencée
                    default => [],
                },
            ),

            'date_fin' => array_merge(
                [$statut === 'resolue' ? 'required' : 'nullable', 'date', 'after:date_debut'],
                $statut === 'resolue' ? ['before_or_equal:now'] : [],
            ),

            'foyers_touches' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'zone_id.required' => 'Veuillez choisir la zone concernée.',
            'zone_id.exists' => 'La zone sélectionnée n\'existe pas.',
            'date_debut.after' => 'Une coupure prévue doit commencer dans le futur.',
            'date_debut.before_or_equal' => 'Une coupure en cours ou résolue doit avoir déjà commencé.',
            'date_fin.required' => 'La date de fin est obligatoire pour une coupure résolue.',
            'date_fin.after' => 'La date de fin doit être après la date de début.',
            'date_fin.before_or_equal' => 'Une coupure résolue ne peut pas se terminer dans le futur.',
        ];
    }

    public function attributes(): array
    {
        return [
            'zone_id' => 'zone',
            'date_debut' => 'date de début',
            'date_fin' => 'date de fin',
            'foyers_touches' => 'nombre de foyers touchés',
        ];
    }
}
