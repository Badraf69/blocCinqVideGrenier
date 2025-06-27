## Base image
#FROM php:8.3-apache
#
## Installation des dépendances système
#RUN apt-get update && apt-get install -y \
#    libzip-dev \
#    zip \
#    unzip \
#    git \
#    curl \
#    python3 \
#    make \
#    g++ \
#    && docker-php-ext-install pdo pdo_mysql zip
#
## Installation de Node.js via NVM
#ENV NODE_VERSION=16.20.0
#ENV NVM_DIR=/root/.nvm
#ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"
#
#RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash \
#    && . "$NVM_DIR/nvm.sh" \
#    && nvm install ${NODE_VERSION} \
#    && nvm use v${NODE_VERSION} \
#    && nvm alias default v${NODE_VERSION}
#
## Vérification de l'installation de Node.js et npm
#RUN node --version \
#    && npm --version
#
## Installation de Composer
#COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
#
## Configuration du répertoire de travail
#WORKDIR /var/www/html
#
## Copie des fichiers de configuration
#COPY composer.* ./
#COPY package*.json ./
#
## Installation des dépendances Composer
#RUN composer install --no-scripts --no-autoloader
#
## Installation de node-sass de manière spécifique
#RUN npm config set unsafe-perm true \
#    && npm install node-sass@4.14.1 --save-dev \
#    && npm install
#
## Copie du reste du code source
#COPY . .
#
## Régénération de l'autoloader Composer
#RUN composer dump-autoload -o
#
## Configuration des permissions
#RUN chown -R www-data:www-data /var/www/html \
#    && chmod -R 755 /var/www/html
#
## Active le module Apache rewrite
#RUN a2enmod rewrite

# Dockerfile de développement
FROM php:8.2-apache

# Installation des extensions PHP nécessaires et outils de dev
RUN apt-get update \
    && apt-get install -y git unzip nano npm \
    && docker-php-ext-install pdo pdo_mysql

# Xdebug pour le debug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Activation du mod_rewrite
RUN a2enmod rewrite

# Copie du code source
COPY . /var/www/html

# Configuration du VirtualHost pour pointer sur /public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Droits
RUN chown -R www-data:www-data /var/www/html


# Expose le port 80
EXPOSE 80

CMD ["apache2-foreground"]
