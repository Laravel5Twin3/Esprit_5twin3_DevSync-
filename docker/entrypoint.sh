#!/bin/sh
# Démarrage du conteneur (utilisateur www-data) : prépare Laravel puis lance Apache.
set -e
cd /var/www/html

if [ -z "$APP_KEY" ]; then
    echo "ERREUR : la variable d'environnement APP_KEY est obligatoire (php artisan key:generate --show)." >&2
    exit 1
fi

# Base SQLite : le fichier doit exister avant les migrations
if [ "$DB_CONNECTION" = "sqlite" ]; then
    mkdir -p "$(dirname "$DB_DATABASE")"
    touch "$DB_DATABASE"
fi

# Optimisations de production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Base de données : schéma à jour, données de démonstration au premier lancement
php artisan migrate --force
php artisan app:seed-si-vide

# Apache écoute sur le port de la variable PORT (8080 par défaut)
exec apache2-foreground
