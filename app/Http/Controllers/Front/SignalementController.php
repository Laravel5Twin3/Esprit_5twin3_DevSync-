<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coupure\SignalementRequest;
use App\Models\Signalement;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SignalementController extends Controller
{
    /**
     * Signalements de l'habitant connecté.
     */
    public function index(Request $request): View
    {
        $signalements = Signalement::with(['zone', 'coupure'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('front.signalements.index', compact('signalements'));
    }

    public function create(Request $request): View
    {
        return view('front.signalements.create', [
            'zones' => Zone::orderBy('nom')->pluck('nom', 'id'),
            'zoneId' => $request->integer('zone_id') ?: null,
        ]);
    }

    public function store(SignalementRequest $request): RedirectResponse
    {
        Signalement::create($request->validated() + [
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('signalements.index')
            ->with('success', 'Merci ! Votre signalement a été envoyé. Il sera vérifié par un administrateur.');
    }
}
