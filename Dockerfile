# Image de production de HeatAlert (Laravel 12, PHP 8.2, Apache)

# ─── Étape 1 : dépendances PHP ────────────────────────────────────────────
FROM composer:2 AS vendor
WORKDIR /app

# Copier d'abord composer.* permet à Docker de réutiliser ce calque tant que les dépendances ne changent pas
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --ignore-platform-reqs

COPY . .
RUN composer dump-autoload --optimize --no-dev

# ─── Étape 2 : image d'exécution ──────────────────────────────────────────
FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libicu-dev \
    && docker-php-ext-install pdo_mysql intl opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Apache sert le dossier public/ de Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Apache écoute sur ${PORT}, lu au démarrage (Render impose le sien) ; un port > 1024
# ne demande pas les droits root, ce qui permet d'exécuter le conteneur sans root
ENV PORT=8080
RUN sed -ri 's/^Listen 80$/Listen ${PORT}/' /etc/apache2/ports.conf \
    && sed -ri 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/' /etc/apache2/sites-available/000-default.conf

# Valeurs par défaut de production (surchargeables par les variables d'environnement de l'hébergeur)
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/var/www/html/database/database.sqlite \
    SESSION_DRIVER=file \
    CACHE_STORE=file \
    QUEUE_CONNECTION=sync

WORKDIR /var/www/html
COPY --from=vendor /app /var/www/html
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Seuls les dossiers où l'application écrit appartiennent à www-data ; le code reste en lecture seule
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data storage bootstrap/cache database \
       /var/run/apache2 /var/lock/apache2 /var/log/apache2

# Le conteneur ne tourne pas en root
USER www-data

EXPOSE 8080
ENTRYPOINT ["entrypoint.sh"]
