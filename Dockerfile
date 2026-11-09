FROM php:8.3-apache

# Bibliothèque système requise pour compiler l'extension PostgreSQL juste après
RUN apt-get update && apt-get install -y --no-install-recommends libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Extension nécessaire pour parler à PostgreSQL (Neon) depuis PHP/PDO
RUN docker-php-ext-install pdo_pgsql

# Le code de l'application
COPY . /var/www/html/
