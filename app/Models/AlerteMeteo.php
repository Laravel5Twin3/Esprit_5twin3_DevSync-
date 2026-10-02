<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlerteMeteo extends Model
{
    use HasFactory;

    public const STATUTS = [
        'en_cours' => 'En cours',
        'a_venir' => 'À venir',
        'terminee' => 'Terminée',
    ];

    protected $fillable = [
        'titre',
        'temperature_max',
        'date_debut',
        'date_fin',
        'message',
        'niveau_vigilance_id',
        'zone_id',
    ];

    protected $casts = [
        'temperature_max' => 'float',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    /**
     * L'alerte appartient à un niveau de vigilance (relation N-1).
     */
    public function niveauVigilance(): BelongsTo
    {
        return $this->belongsTo(NiveauVigilance::class);
    }

    /**
     * L'alerte concerne une zone (table du module Coupures).
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────

    /** Alertes pas encore terminées (en cours + à venir). */
    public function scopeActives(Builder $query): Builder
    {
        return $query->where('date_fin', '>=', now());
    }

    public function scopeEnCours(Builder $query): Builder
    {
        return $query->where('date_debut', '<=', now())->where('date_fin', '>=', now());
    }

    public function scopeAVenir(Builder $query): Builder
    {
        return $query->where('date_debut', '>', now());
    }

    public function scopeTerminees(Builder $query): Builder
    {
        return $query->where('date_fin', '<', now());
    }

    /** Filtre par statut calculé : en_cours | a_venir | terminee. */
    public function scopeStatut(Builder $query, ?string $statut): Builder
    {
        return match ($statut) {
            'en_cours' => $query->enCours(),
            'a_venir' => $query->aVenir(),
            'terminee' => $query->terminees(),
            default => $query,
        };
    }

    // ─── Accesseurs ───────────────────────────────────────────────────────

    /** Statut calculé automatiquement à partir des dates. */
    public function getStatutAttribute(): string
    {
        return match (true) {
            $this->date_debut->isFuture() => 'a_venir',
            $this->date_fin->isPast() => 'terminee',
            default => 'en_cours',
        };
    }

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut];
    }

    public function getStatutCouleurAttribute(): string
    {
        return match ($this->statut) {
            'en_cours' => 'danger',
            'a_venir' => 'info',
            default => 'secondary',
        };
    }
}
