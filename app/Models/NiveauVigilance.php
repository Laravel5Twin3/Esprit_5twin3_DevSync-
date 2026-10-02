<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NiveauVigilance extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'couleur',
        'temperature_min',
        'temperature_max',
        'consigne',
        'ordre',
    ];

    protected $casts = [
        'temperature_min' => 'float',
        'temperature_max' => 'float',
        'ordre' => 'integer',
    ];

    /**
     * Un niveau de vigilance a plusieurs alertes (relation 1-N).
     */
    public function alertes(): HasMany
    {
        return $this->hasMany(AlerteMeteo::class);
    }

    /**
     * Trouve le niveau correspondant à une température (niveau automatique).
     * On prend le niveau le plus grave dont le seuil minimum est atteint.
     */
    public static function pourTemperature(float $temperature): ?self
    {
        return static::where('temperature_min', '<=', $temperature)
            ->orderByDesc('temperature_min')
            ->first()
            ?? static::orderBy('temperature_min')->first();
    }

    /** Plage de température lisible, ex : « 38 – 41,9 °C » ou « ≥ 44 °C ». */
    public function getPlageAttribute(): string
    {
        $min = rtrim(rtrim(number_format($this->temperature_min, 1, ',', ''), '0'), ',');

        if ($this->temperature_max === null) {
            return "≥ {$min} °C";
        }

        $max = rtrim(rtrim(number_format($this->temperature_max, 1, ',', ''), '0'), ',');

        return "{$min} – {$max} °C";
    }

    /** Couleur du texte lisible sur le fond du badge (noir sur jaune, blanc sinon). */
    public function getCouleurTexteAttribute(): string
    {
        $hex = ltrim($this->couleur ?? '#6c757d', '#');
        [$r, $g, $b] = array_map('hexdec', str_split(str_pad($hex, 6, '0'), 2));

        return (0.299 * $r + 0.587 * $g + 0.114 * $b) > 160 ? '#212529' : '#ffffff';
    }
}
