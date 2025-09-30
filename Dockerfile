FROM php:8.4-apache

## Installer extensions PHP
RUN docker-php-ext-install pdo pdo_mysql

## Activer mod_rewrite pour Laravel / routes API
RUN a2enmod rewrite

## Installer dépendances système
RUN apt-get update && \
    apt-get install -y git unzip zip libzip-dev curl && \
    docker-php-ext-install zip && \
    rm -rf /var/lib/apt/lists/*

## Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

## Définir le répertoire de travail
WORKDIR /var/www/html

## Copier uniquement composer.* pour optimiser cache Docker
COPY composer.json composer.lock /var/www/html/

## Installer dépendances PHP
RUN composer install --no-dev --optimize-autoloader

## Copier le reste du projet
COPY . /var/www/html

## Copier config Apache personnalisée
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default.conf

# Générer la spec OpenAPI (avant de passer en /public)
RUN ./vendor/bin/openapi --output public/openapi.json App routes || true

# Installer Swagger UI statique
RUN mkdir -p /var/www/html/public/swagger \
    && curl -L https://github.com/swagger-api/swagger-ui/archive/refs/tags/v5.11.8.tar.gz | tar xz -C /tmp \
    && cp -r /tmp/swagger-ui-5.11.8/dist/* /var/www/html/public/swagger/ \
    && rm -rf /tmp/swagger-ui-5.11.8

# Configurer Swagger UI pour pointer sur notre openapi.json
RUN sed -i "s|url: \"https://petstore.swagger.io/v2/swagger.json\"|url: \"/openapi.json\"|" /var/www/html/public/swagger/swagger-initializer.js

# Définir le répertoire public comme racine
WORKDIR /var/www/html/public

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80
