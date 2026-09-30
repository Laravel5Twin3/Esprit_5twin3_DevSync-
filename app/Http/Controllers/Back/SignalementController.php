<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Signalement;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SignalementController extends Controller
{
    public function index(Request $request): View
    {
        $statut = $request->input('statut', 'en_attente');

        $signalements = Signalement::with(['user', 'zone', 'coupure'])
            ->when($statut !== 'tous', fn ($q) => $q->where('statut', $statut))
            ->when($request->filled('zone_id'), fn ($q) => $q->where('zone_id', $request->zone_id))
            ->latest('date_constat')
            ->paginate(15)
            ->withQueryString();

        return view('back.signalements.index', [
            'signalements' => $signalements,
            'statut' => $statut,
            'zones' => Zone::orderBy('nom')->pluck('nom', 'id'),
            'compteurs' => Signalement::selectRaw('statut, count(*) as total')->groupBy('statut')->pluck('total', 'statut'),
        ]);
    }

    public function valider(Signalement $signalement): RedirectResponse
    {
        if ($signalement->statut !== 'en_attente') {
            return back()->with('error', 'Ce signalement a déjà été traité.');
        }

        ['coupure' => $coupure, 'creee' => $creee, 'rattaches' => $rattaches] = $signalement->valider();

        $message = $creee
            ? "Coupure #{$coupure->id} créée dans la zone {$coupure->zone->nom}"
            : "Signalement rattaché à la coupure en cours #{$coupure->id}";

        return redirect()
            ->route('admin.signalements.index')
            ->with('success', $message.($rattaches > 1 ? " ({$rattaches} signalements regroupés)." : '.'));
    }

    public function rejeter(Signalement $signalement): RedirectResponse
    {
        if ($signalement->statut !== 'en_attente') {
            return back()->with('error', 'Ce signalement a déjà été traité.');
        }

        $signalement->update(['statut' => 'rejete']);

        return back()->with('success', 'Signalement rejeté.');
    }

    public function destroy(Signalement $signalement): RedirectResponse
    {
        $signalement->delete();

        return back()->with('success', 'Signalement supprimé.');
    }
}
