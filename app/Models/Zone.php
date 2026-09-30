<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    use HasFactory;

    public const NIVEAUX_RISQUE = [
        'faible' => 'Faible',
        'moyen' => 'Moyen',
        'eleve' => 'Élevé',
    ];

    public const GOUVERNORATS = [
        'Ariana', 'Béja', 'Ben Arous', 'Bizerte', 'Gabès', 'Gafsa', 'Jendouba', 'Kairouan',
        'Kasserine', 'Kébili', 'Le Kef', 'Mahdia', 'La Manouba', 'Médenine', 'Monastir', 'Nabeul',
        'Sfax', 'Sidi Bouzid', 'Siliana', 'Sousse', 'Tataouine', 'Tozeur', 'Tunis', 'Zaghouan',
    ];

    protected $fillable = [
        'nom',
        'gouvernorat',
        'code_postal',
        'population',
        'latitude',
        'longitude',
        'niveau_risque',
        'description',
    ];

    protected $casts = [
        'population' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Une zone a plusieurs coupures (relation 1-N).
     */
    public function coupures(): HasMany
    {
        return $this->hasMany(Coupure::class);
    }

    /**
     * Signalements des habitants dans cette zone (relation 1-N).
     */
    public function signalements(): HasMany
    {
        return $this->hasMany(Signalement::class);
    }

    public function getNiveauRisqueLabelAttribute(): string
    {
        return self::NIVEAUX_RISQUE[$this->niveau_risque] ?? $this->niveau_risque;
    }

    public function getNiveauRisqueCouleurAttribute(): string
    {
        return match ($this->niveau_risque) {
            'faible' => 'success',
            'moyen' => 'warning',
            'eleve' => 'danger',
            default => 'secondary',
        };
    }
}
