<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriePoint extends Model
{
    use HasFactory;

    protected $table = 'categorie_points';

    protected $fillable = [
        'nom',
        'icone',
        'couleur',
        'description',
    ];

    /**
     * Une catégorie a plusieurs points de fraîcheur (relation 1-N).
     */
    public function pointsFraicheur(): HasMany
    {
        return $this->hasMany(PointFraicheur::class, 'categorie_point_id');
    }

    /**
     * Nombre de points actifs dans cette catégorie.
     */
    public function nombrePointsActifs(): int
    {
        return $this->pointsFraicheur()->where('actif', true)->count();
    }
}
