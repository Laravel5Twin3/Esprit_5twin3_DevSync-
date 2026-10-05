<?php

namespace App\Http\Requests\Conseil;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategorieConseilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'         => ['required', 'string', 'min:2', 'max:80', 'unique:categorie_conseils,nom'],
            'icone'       => ['nullable', 'string', 'max:60'],
            'couleur'     => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required'  => 'Le nom de la catégorie est obligatoire.',
            'nom.unique'    => 'Cette catégorie existe déjà.',
            'nom.min'       => 'Le nom doit contenir au moins :min caractères.',
            'couleur.regex' => 'La couleur doit être au format hexadécimal (#RRGGBB).',
        ];
    }
}
