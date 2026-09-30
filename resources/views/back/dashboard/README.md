# Blocs du tableau de bord

Chaque module peut ajouter **son propre fichier** ici (ex. `coupures.blade.php`) : il est inclus
automatiquement dans le tableau de bord admin, sous les statistiques communes (ordre alphabétique).

Conseil : mettre les requêtes dans une classe de composant Blade (`php artisan make:component MonModule/Statistiques`)
plutôt que directement dans la vue, puis n'écrire ici que `<x-mon-module.statistiques />`.
