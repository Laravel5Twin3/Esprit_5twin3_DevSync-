@extends('layouts.back')

@section('title', 'Utilisateurs')
@section('page-title', 'Utilisateurs et rôles')

@section('content')
    {{-- Rôles et permissions (Spatie Permission) --}}
    <div class="row g-3 mb-4">
        @foreach ($roles as $role)
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h2 class="h6 mb-2">
                            <span class="badge {{ $role->name === 'admin' ? 'bg-danger' : 'bg-secondary' }}">{{ ucfirst($role->name) }}</span>
                            <span class="text-muted small ms-1">{{ $role->permissions->count() }} permissions</span>
                        </h2>
                        @foreach ($role->permissions->sortBy('name') as $permission)
                            <span class="badge rounded-pill text-bg-light border fw-normal">{{ $permission->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <form method="GET" class="card card-body shadow-sm border-0 mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label for="q" class="form-label small">Recherche</label>
                <input type="search" id="q" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nom ou e-mail">
            </div>
            <div class="col-md-4">
                <label for="role" class="form-label small">Rôle</label>
                <select id="role" name="role" class="form-select">
                    <option value="">Tous</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-dark flex-grow-1" type="submit"><i class="bi bi-funnel"></i> Filtrer</button>
                <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-outline-secondary" title="Réinitialiser"><i class="bi bi-x-lg"></i></a>
            </div>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Comptes</span>
            <span class="badge bg-secondary">{{ $utilisateurs->total() }} utilisateur(s)</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Utilisateur</th>
                        <th>E-mail vérifié</th>
                        <th>2FA</th>
                        <th>Inscrit le</th>
                        <th>Rôle</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($utilisateurs as $utilisateur)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <x-avatar :user="$utilisateur" size="36" />
                                    <div>
                                        <div class="fw-semibold">{{ $utilisateur->name }}</div>
                                        <div class="small text-muted">{{ $utilisateur->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($utilisateur->email_verified_at)
                                    <span class="badge bg-success-subtle text-success-emphasis"><i class="bi bi-check"></i> Oui</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis">En attente</span>
                                @endif
                            </td>
                            <td>
                                @if ($utilisateur->aActiveLa2fa())
                                    <i class="bi bi-shield-check text-success" title="Activée"></i>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="small">{{ $utilisateur->created_at->format('d/m/Y') }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.utilisateurs.role', $utilisateur) }}" class="d-flex gap-1">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" class="form-select form-select-sm" aria-label="Rôle de {{ $utilisateur->name }}"
                                            @disabled($utilisateur->is(auth()->user()))>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}" @selected($utilisateur->hasRole($role->name))>{{ ucfirst($role->name) }}</option>
                                        @endforeach
                                    </select>
                                    @unless ($utilisateur->is(auth()->user()))
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Enregistrer le rôle"><i class="bi bi-check-lg"></i></button>
                                    @endunless
                                </form>
                            </td>
                            <td class="text-end">
                                @if ($utilisateur->is(auth()->user()))
                                    <span class="small text-muted">vous</span>
                                @else
                                    <x-delete-button :action="route('admin.utilisateurs.destroy', $utilisateur)" label=""
                                                     :confirm="'Supprimer le compte de '.$utilisateur->name.' ?'" />
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($utilisateurs->hasPages())
            <div class="card-footer bg-white d-flex justify-content-end">{{ $utilisateurs->links() }}</div>
        @endif
    </div>
@endsection
