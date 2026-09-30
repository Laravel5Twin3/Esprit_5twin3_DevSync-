<?php

namespace App\Http\Requests\Coupure;

use App\Models\Signalement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Validation du signalement d'une coupure par un habitant.
 */
class SignalementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'zone_id' => ['required', 'integer', 'exists:zones,id'],
            // On ne signale que ce qu'on constate : dans les dernières 24 h, pas dans le futur
            'date_constat' => [
                'required', 'date',
                'before_or_equal:now',
                'after_or_equal:'.now()->subDay()->format('Y-m-d H:i'),
            ],
            'description' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    /**
     * Anti-doublon : un même habitant ne peut pas signaler deux fois la même zone en moins d'une heure.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $doublon = Signalement::where('user_id', $this->user()->id)
                    ->where('zone_id', $this->input('zone_id'))
                    ->where('created_at', '>=', now()->subHour())
                    ->exists();

                if ($doublon) {
                    $validator->errors()->add('zone_id', 'Vous avez déjà signalé une coupure dans cette zone il y a moins d\'une heure.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'zone_id.required' => 'Indiquez la zone où vous constatez la coupure.',
            'date_constat.before_or_equal' => 'Vous ne pouvez pas signaler une coupure dans le futur.',
            'date_constat.after_or_equal' => 'Le signalement doit concerner une coupure constatée dans les dernières 24 heures.',
            'description.min' => 'Décrivez la situation en au moins 10 caractères (ex. : « plus de courant dans tout l\'immeuble »).',
        ];
    }

    public function attributes(): array
    {
        return [
            'zone_id' => 'zone',
            'date_constat' => 'heure du constat',
        ];
    }
}
