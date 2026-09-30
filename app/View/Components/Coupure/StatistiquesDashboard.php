<?php

namespace App\View\Components\Coupure;

use App\Models\Coupure;
use App\Models\Signalement;
use App\Services\Coupure\PredictionCoupureService;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Bloc « Coupures de courant » du tableau de bord admin.
 */
class StatistiquesDashboard extends Component
{
    public int $enCours;

    public int $prevues;

    public int $foyers;

    public int $signalementsEnAttente;

    /** Zone la plus exposée demain selon le modèle de prédiction (ou null). */
    public ?array $risqueDemain = null;

    public function __construct(PredictionCoupureService $prediction)
    {
        $this->enCours = Coupure::where('statut', 'en_cours')->count();
        $this->prevues = Coupure::where('statut', 'prevue')
            ->whereBetween('date_debut', [now(), now()->addDays(7)])
            ->count();
        $this->foyers = (int) Coupure::where('statut', 'en_cours')->sum('foyers_touches');
        $this->signalementsEnAttente = Signalement::enAttente()->count();

        try {
            $this->risqueDemain = $prediction->previsionPourDate(today()->addDay())['resultats']->first();
        } catch (\Throwable) {
            // La prédiction est un bonus : le tableau de bord doit s'afficher même si elle échoue
        }
    }

    public function render(): View
    {
        return view('components.coupure.statistiques-dashboard');
    }
}
