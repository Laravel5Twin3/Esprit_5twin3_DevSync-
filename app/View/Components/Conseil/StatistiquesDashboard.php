<?php

namespace App\View\Components\Conseil;

use App\Models\CategorieConseil;
use App\Models\Conseil;
use Illuminate\View\Component;
use Illuminate\View\View;

class StatistiquesDashboard extends Component
{
    public int $total;

    public int $actifs;

    public int $urgents;

    public int $categories;

    public function __construct()
    {
        $this->total = Conseil::count();
        $this->actifs = Conseil::where('actif', true)->count();
        $this->urgents = Conseil::where('priorite', 'urgent')->where('actif', true)->count();
        $this->categories = CategorieConseil::count();
    }

    public function render(): View
    {
        return view('components.conseil.statistiques-dashboard');
    }
}
