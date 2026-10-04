<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategorieConseil extends Model
{
    use HasFactory;

    protected $table = 'categorie_conseils';

    protected $fillable = [
        'nom',
        'icone',
        'couleur',
        'description',
    ];

    /**
     * Une catégorie a plusieurs conseils (relation 1-N).
     */
    public function conseils(): HasMany
    {
        return $this->hasMany(Conseil::class, 'categorie_conseil_id');
    }

    public function nombreConseilsActifs(): int
    {
        return $this->conseils()->where('actif', true)->count();
    }
}
