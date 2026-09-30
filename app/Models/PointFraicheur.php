<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointFraicheur extends Model
{
    use HasFactory;

    protected $table = 'point_fraicheurs';

    protected $fillable = [
        'categorie_point_id',
        'nom',
        'adresse',
        'latitude',
        'longitude',
        'horaires',
        'capacite',
        'actif',
        'description',
    ];

    protected $casts = [
        'actif'     => 'boolean',
        'latitude'  => 'decimal:7',
        'longitude' => 'decimal:7',
        'capacite'  => 'integer',
    ];

    /**
     * Un point de fraîcheur appartient à une catégorie (relation N-1).
     */
    public function categoriePoint(): BelongsTo
    {
        return $this->belongsTo(CategoriePoint::class, 'categorie_point_id');
    }

    /**
     * Calcule la distance (en km) entre ce point et des coordonnées données.
     * Formule de Haversine.
     */
    public function distanceTo(float $lat, float $lng): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat - $this->latitude);
        $dLng = deg2rad($lng - $this->longitude);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($this->latitude))
            * cos(deg2rad($lat))
            * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * asin(sqrt($a));
    }

    /**
     * Scope : uniquement les points actifs.
     */
    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }
}
