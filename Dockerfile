FROM php:8.2-apache

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libicu-dev libzip-dev zip unzip git curl libonig-dev libpq-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache

# Activer mod_rewrite d'Apache
RUN a2enmod rewrite

# Copier Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier les fichiers du projet
COPY . .

# Fixer les permissions
RUN chown -R www-data:www-data /var/www/html

# Mettre à jour la configuration Apache pour servir le dossier public/
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Définir l'environnement
ENV APP_ENV=dev
