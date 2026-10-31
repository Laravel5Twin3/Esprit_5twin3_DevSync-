<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * Gestion des utilisateurs par l'administrateur : liste, changement de rôle, suppression.
 * Accès protégé côté serveur par la permission « gerer utilisateurs » (routes/web.php).
 */
class UtilisateurController extends Controller
{
    public function index(Request $request): View
    {
        $utilisateurs = User::query()
            ->with('roles')
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', '%'.$request->q.'%')
                ->orWhere('email', 'like', '%'.$request->q.'%')))
            ->when($request->filled('role'), fn ($q) => $q->role($request->role))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('back.utilisateurs.index', [
            'utilisateurs' => $utilisateurs,
            'roles' => Role::with('permissions')->orderBy('name')->get(),
        ]);
    }

    public function changerRole(Request $request, User $utilisateur): RedirectResponse
    {
        $donnees = $request->validate([
            'role' => ['required', Rule::exists('roles', 'name')],
        ]);

        if ($utilisateur->is($request->user()) && $donnees['role'] !== 'admin') {
            return back()->with('error', 'Vous ne pouvez pas retirer votre propre rôle administrateur.');
        }

        $utilisateur->syncRoles($donnees['role']);

        return back()->with('success', "{$utilisateur->name} a maintenant le rôle « {$donnees['role']} ».");
    }

    public function destroy(Request $request, User $utilisateur): RedirectResponse
    {
        if ($utilisateur->is($request->user())) {
            return back()->with('error', 'Supprimez votre propre compte depuis votre profil.');
        }

        if ($utilisateur->photo_path) {
            Storage::disk('public')->delete($utilisateur->photo_path);
        }
        $utilisateur->delete();

        return back()->with('success', 'Utilisateur supprimé.');
    }
}
