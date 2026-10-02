<?php

namespace App\View\Components\AlerteMeteo;

use App\Models\AlerteMeteo;
use App\Models\NiveauVigilance;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Bloc « Alertes météo » du tableau de bord admin.
 */
class StatistiquesDashboard extends Component
{
    public int $enCours;

    public int $aVenir;

    public ?float $temperatureMax;

    public int $zonesTouchees;

    /** Niveaux avec le nombre d'alertes actives (pour le graphique). */
    public Collection $parNiveau;

    public function __construct()
    {
        $this->enCours = AlerteMeteo::enCours()->count();
        $this->aVenir = AlerteMeteo::aVenir()->count();
        $this->temperatureMax = AlerteMeteo::enCours()->max('temperature_max');
        $this->zonesTouchees = AlerteMeteo::enCours()->distinct('zone_id')->count('zone_id');
        $this->parNiveau = NiveauVigilance::withCount(['alertes' => fn ($q) => $q->actives()])
            ->orderBy('ordre')
            ->get();
    }

    public function render(): View
    {
        return view('components.alerte-meteo.statistiques-dashboard');
    }
}
