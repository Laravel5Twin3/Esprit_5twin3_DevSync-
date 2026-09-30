<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupure extends Model
{
    use HasFactory;

    public const TYPES = [
        'delestage' => 'Délestage',
        'panne' => 'Panne',
        'maintenance' => 'Maintenance',
        'surcharge' => 'Surcharge réseau',
    ];

    public const STATUTS = [
        'prevue' => 'Prévue',
        'en_cours' => 'En cours',
        'resolue' => 'Résolue',
        'annulee' => 'Annulée',
    ];

    protected $fillable = [
        'zone_id',
        'titre',
        'type',
        'statut',
        'date_debut',
        'date_fin',
        'foyers_touches',
        'description',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'foyers_touches' => 'integer',
    ];

    /**
     * Une coupure appartient à une zone (relation N-1).
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    /**
     * Signalements des habitants rattachés à cette coupure (relation 1-N).
     */
    public function signalements(): HasMany
    {
        return $this->hasMany(Signalement::class);
    }

    /**
     * Coupures qui concernent les habitants : en cours ou prévues.
     */
    public function scopeActives(Builder $query): Builder
    {
        return $query->whereIn('statut', ['en_cours', 'prevue']);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    public function getStatutCouleurAttribute(): string
    {
        return match ($this->statut) {
            'en_cours' => 'danger',
            'prevue' => 'warning',
            'resolue' => 'success',
            default => 'secondary',
        };
    }

    public function getTypeIconeAttribute(): string
    {
        return match ($this->type) {
            'delestage' => 'bi-lightning-charge',
            'panne' => 'bi-exclamation-octagon',
            'maintenance' => 'bi-tools',
            'surcharge' => 'bi-graph-up-arrow',
            default => 'bi-lightning',
        };
    }

    /**
     * Durée lisible de la coupure (ex. "2 h 30 min"), ou null si la fin est inconnue.
     */
    public function getDureeAttribute(): ?string
    {
        if (! $this->date_fin) {
            return null;
        }

        $minutes = (int) $this->date_debut->diffInMinutes($this->date_fin);

        return $minutes >= 60
            ? intdiv($minutes, 60).' h'.($minutes % 60 ? ' '.($minutes % 60).' min' : '')
            : $minutes.' min';
    }
}
