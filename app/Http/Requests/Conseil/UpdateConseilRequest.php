<?php

namespace App\Http\Requests\Conseil;

use App\Models\Conseil;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConseilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categorie_conseil_id' => ['required', 'exists:categorie_conseils,id'],
            'titre'                => ['required', 'string', 'min:5', 'max:150'],
            'resume'               => ['nullable', 'string', 'max:255'],
            'contenu'              => ['required', 'string', 'min:20', 'max:5000'],
            'public_cible'         => ['required', Rule::in(array_keys(Conseil::PUBLICS))],
            'priorite'             => ['required', Rule::in(array_keys(Conseil::PRIORITES))],
            'actif'                => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'categorie_conseil_id.required' => 'Veuillez sélectionner une catégorie.',
            'categorie_conseil_id.exists'   => 'La catégorie sélectionnée est invalide.',
            'titre.required'                => 'Le titre du conseil est obligatoire.',
            'titre.min'                     => 'Le titre doit contenir au moins :min caractères.',
            'contenu.required'              => 'Le contenu du conseil est obligatoire.',
            'contenu.min'                   => 'Le contenu doit contenir au moins :min caractères.',
            'public_cible.required'         => 'Veuillez indiquer le public concerné.',
            'public_cible.in'               => 'Le public concerné est invalide.',
            'priorite.required'             => 'Veuillez indiquer la priorité.',
            'priorite.in'                   => 'La priorité est invalide.',
        ];
    }
}
