#!/bin/sh
# Démarrage du conteneur : prépare Laravel puis lance Apache.
set -e
cd /var/www/html

# L'hébergeur (Render…) impose le port d'écoute via la variable PORT
PORT="${PORT:-80}"
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

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

# Les commandes ci-dessus tournent en root : Apache (www-data) doit pouvoir écrire
chown -R www-data:www-data storage bootstrap/cache database

exec apache2-foreground
