# HeatAlert 🌡️⚡

Application web qui aide les habitants d'un quartier ou d'une résidence à anticiper les épisodes
de **canicule** et les **coupures de courant** associées (délestage, surcharge du réseau).

Elle centralise les alertes météo, signale les coupures en cours ou prévues, recense les points de
fraîcheur accessibles à proximité (parcs, salles climatisées, fontaines) et propose des conseils
adaptés (économie d'énergie, hydratation, gestion des équipements sensibles).

> Projet Esprit — 5TWIN3 — équipe **DevSync** — Laravel 12 · Blade · MySQL · Bootstrap 5

---

## Modules

| # | Module | Branche |
|---|---|---|
| — | Utilisateurs (commun) | — |
| 1 | Alertes météo / canicule | `feature/alertes-meteo` |
| 2 | Coupures de courant | `feature/coupures` |
| 3 | Points de fraîcheur | `feature/points-fraicheur` |
| 4 | Conseils et prévention | `feature/conseils` |
| 5 | Entraide entre voisins | `feature/entraide` |

---

## Installation (chaque membre)

Prérequis : PHP ≥ 8.2, Composer, Node/npm, XAMPP (MySQL démarré).

1. Créer la base **`heatalert`** dans phpMyAdmin (interclassement `utf8mb4_unicode_ci`).
2. Puis :

```bash
git clone <url-du-repo>
cd Esprit_5twin3_DevSync-
npm run setup     # composer install + .env + clé + migrations + données de test (une seule fois)
npm run dev       # lance le serveur
```

Ouvrir http://127.0.0.1:8000

### Commandes npm

| Commande | Rôle |
|---|---|
| `npm run setup` | Première installation (composer install, `.env`, clé, base + données) |
| `npm run dev` / `npm start` | Lance le serveur sur http://127.0.0.1:8000 |
| `npm run db:fresh` | Recrée la base et relance les seeders |
| `npm run update` | Après un `git pull` : composer install + vide les caches + recrée la base |
| `npm run clear` | Vide les caches (config, routes, vues) |
| `npm run routes` | Liste les routes du projet |


| Compte                 | Email                  | Mot de passe |
| ---------------------- | ---------------------- | ------------ |
| Admin (Back Office)    | `admin@heatalert.tn`   | `password`   |
| Citoyen (Front Office) | `citoyen@heatalert.tn` | `password`   |

---

## Architecture commune (déjà en place)


| Élément                        | Emplacement                                                                                                                                                   |
| -------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Layout Front Office              | `resources/views/layouts/front.blade.php`                                                                                                                     |
| Layout Back Office               | `resources/views/layouts/back.blade.php`                                                                                                                      |
| Partials                         | `resources/views/front/partials/`, `resources/views/back/partials/`                                                                                           |
| Composants Blade                 | `resources/views/components/` → `<x-card>`, `<x-flash>`, `<x-delete-button>`, `<x-form.input>`, `<x-form.select>`, `<x-form.textarea>`, `<x-back.stat-card>` |
| Auth (login / register / logout) | `app/Http/Controllers/Auth/AuthController.php`                                                                                                                |
| Rôles                           | colonne`users.role` (`admin` / `citoyen`), middleware `admin`                                                                                                 |
| Routes admin                     | préfixe`/admin`, noms `admin.*`, middleware `['auth', 'admin']`                                                                                              |

### Sections disponibles dans les layouts

- **Front** : `@section('title')`, `@section('hero')` (optionnel), `@section('content')`, `@push('styles')`, `@push('scripts')`
- **Back** : `@section('title')`, `@section('page-title')`, `@section('page-actions')` (boutons en haut à droite), `@section('content')`, `@push('styles')`, `@push('scripts')`

---

## Règles pour éviter les conflits Git ⚠️

Chaque module vit dans **ses propres fichiers**. Les fichiers communs sont chargés automatiquement :


| Quoi                           | Où créer VOTRE fichier                                            | Chargé automatiquement ?           |
| ------------------------------ | ------------------------------------------------------------------- | ----------------------------------- |
| Routes                         | `routes/modules/<module>.php`                                       | ✅ (voir`routes/modules/README.md`) |
| Lien menu Front                | `resources/views/front/menu/<module>.blade.php`                     | ✅                                  |
| Lien menu Back                 | `resources/views/back/menu/<module>.blade.php`                      | ✅                                  |
| Contrôleurs                   | `app/Http/Controllers/Front/…`, `app/Http/Controllers/Back/…`     | —                                  |
| Validation                     | `app/Http/Requests/<Module>/…Request.php`                          | —                                  |
| Vues                           | `resources/views/front/<module>/`, `resources/views/back/<module>/` | —                                  |
| Modèles / Factories / Seeders | `app/Models/`, `database/factories/`, `database/seeders/`           | —                                  |

Seul fichier commun à toucher : `database/seeders/DatabaseSeeder.php` → **une seule ligne** par module
dans `$this->call([...])`.

Ne modifiez **pas** les layouts, `routes/web.php`, `User` ou les composants communs sans prévenir l'équipe.

Générer les fichiers d'un module (exemple) :

```bash
php artisan make:model Zone -mfs                     # modèle + migration + factory + seeder
php artisan make:model Coupure -mfs
php artisan make:controller Back/CoupureController --resource --model=Coupure
php artisan make:controller Front/CoupureController
php artisan make:request Coupure/StoreCoupureRequest
php artisan make:request Coupure/UpdateCoupureRequest
```

---

## Workflow Git

```bash
# 1. Créer sa branche depuis main (une seule fois)
git checkout main
git pull origin main
git checkout -b feature/<module>

# 2. Travailler et commiter régulièrement
git add .
git commit -m "feat(coupures): CRUD zones"
git push -u origin feature/<module>

# 3. Récupérer les nouveautés de main dans sa branche (souvent !)
git checkout main
git pull origin main
git checkout feature/<module>
git merge main

# 4. Intégrer : ouvrir une Pull Request feature/<module> -> main sur GitHub
```

Après chaque `git pull` : `npm run update`
(de nouvelles migrations ou dépendances ont pu être ajoutées par les autres).
