<?php

namespace App\Http\Requests\Conseil;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategorieConseilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'         => ['required', 'string', 'min:2', 'max:80',
                              Rule::unique('categorie_conseils', 'nom')->ignore($this->route('categorieConseil'))],
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
            'couleur.regex' => 'La couleur doit être au format hexadécimal (#RRGGBB).',
        ];
    }
}
