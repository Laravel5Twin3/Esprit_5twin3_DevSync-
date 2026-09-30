<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Coupure constatée et signalée par un habitant, en attente de vérification par un admin.
 */
class Signalement extends Model
{
    use HasFactory;

    public const STATUTS = [
        'en_attente' => 'En attente',
        'valide' => 'Validé',
        'rejete' => 'Rejeté',
    ];

    /** Écart maximal entre deux signalements d'une même zone pour qu'ils concernent la même coupure. */
    public const FENETRE_HEURES = 3;

    protected $fillable = [
        'user_id',
        'zone_id',
        'coupure_id',
        'date_constat',
        'description',
        'statut',
    ];

    protected $casts = [
        'date_constat' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function coupure(): BelongsTo
    {
        return $this->belongsTo(Coupure::class);
    }

    public function scopeEnAttente(Builder $query): Builder
    {
        return $query->where('statut', 'en_attente');
    }

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    public function getStatutCouleurAttribute(): string
    {
        return match ($this->statut) {
            'en_attente' => 'warning',
            'valide' => 'success',
            'rejete' => 'secondary',
            default => 'light',
        };
    }

    /**
     * Valide le signalement : il est rattaché à la coupure en cours dans la zone,
     * ou une nouvelle coupure est créée. Les autres signalements en attente de la même
     * zone, constatés à moins de 3 h d'intervalle, sont rattachés à la même coupure.
     *
     * @return array{coupure: Coupure, creee: bool, rattaches: int}
     */
    public function valider(): array
    {
        $coupure = Coupure::where('zone_id', $this->zone_id)
            ->where('statut', 'en_cours')
            ->latest('date_debut')
            ->first();

        $creee = false;
        if (! $coupure) {
            $coupure = Coupure::create([
                'zone_id' => $this->zone_id,
                'titre' => 'Coupure signalée par les habitants',
                'type' => 'panne',
                'statut' => 'en_cours',
                'date_debut' => $this->date_constat,
                'description' => $this->description,
            ]);
            $creee = true;
        }

        $rattaches = static::enAttente()
            ->where('zone_id', $this->zone_id)
            ->whereBetween('date_constat', [
                $this->date_constat->copy()->subHours(self::FENETRE_HEURES),
                $this->date_constat->copy()->addHours(self::FENETRE_HEURES),
            ])
            ->update(['statut' => 'valide', 'coupure_id' => $coupure->id]);

        return ['coupure' => $coupure, 'creee' => $creee, 'rattaches' => $rattaches];
    }
}
