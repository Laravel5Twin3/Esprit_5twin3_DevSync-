<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Services\Coupure\PredictionCoupureService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class PredictionCoupureController extends Controller
{
    public function __construct(private PredictionCoupureService $prediction) {}

    public function index(): View
    {
        return view('back.coupures.ia', [
            'infos' => $this->prediction->informations(),
            'previsions' => $this->prediction->previsions(),
        ]);
    }

    public function entrainer(): RedirectResponse
    {
        try {
            $infos = $this->prediction->entrainer();
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Modèle réentraîné sur {$infos['metriques']['exemples']} exemples.");
    }
}
