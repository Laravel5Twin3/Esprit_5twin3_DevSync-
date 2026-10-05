<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conseil extends Model
{
    use HasFactory;

    protected $table = 'conseils';

    public const PUBLICS = [
        'tous'                 => 'Tout le monde',
        'personnes_agees'      => 'Personnes âgées',
        'enfants'              => 'Enfants',
        'malades_chroniques'   => 'Personnes fragiles / malades chroniques',
    ];

    public const PRIORITES = [
        'info'      => 'Information',
        'important' => 'Important',
        'urgent'    => 'Urgent',
    ];

    protected $fillable = [
        'categorie_conseil_id',
        'titre',
        'resume',
        'contenu',
        'public_cible',
        'priorite',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    /**
     * Un conseil appartient à une catégorie (relation N-1).
     */
    public function categorieConseil(): BelongsTo
    {
        return $this->belongsTo(CategorieConseil::class, 'categorie_conseil_id');
    }

    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }

    public function labelPublicCible(): string
    {
        return self::PUBLICS[$this->public_cible] ?? $this->public_cible;
    }

    public function labelPriorite(): string
    {
        return self::PRIORITES[$this->priorite] ?? $this->priorite;
    }

    public function badgePriorite(): string
    {
        return match ($this->priorite) {
            'urgent'    => 'danger',
            'important' => 'warning',
            default     => 'info',
        };
    }
}
