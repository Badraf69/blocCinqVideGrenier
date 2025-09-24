FROM php:8.4-apache

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install system dependencies for Composer
RUN apt-get update && \
    apt-get install -y git unzip zip libzip-dev && \
    docker-php-ext-install zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Installer les dépendances PHP
COPY composer.json composer.lock /var/www/html/
RUN composer install --no-dev --optimize-autoloader
COPY . /var/www/html

# Copier un VirtualHost personnalisé pour Apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default.conf

# Ajoute la config msmtp pour MailHog
#COPY msmtprc /etc/msmtprc
#RUN chmod 600 /etc/msmtprc

# Configure PHP pour utiliser msmtp comme sendmail
#RUN echo 'sendmail_path = "/usr/bin/msmtp -C /etc/msmtprc --logfile /var/log/msmtp.log --aliases=/etc/aliases -t"' >> /usr/local/etc/php/php.ini

# Ajoute la config PHP personnalisée pour MailHog
#COPY php.ini /usr/local/etc/php/

# Définir le répertoire public comme racine
WORKDIR /var/www/html/public

# Changer les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Installe swagger-ui (version statique)
#RUN mkdir -p /var/www/html/public/swagger \
#    && curl -L https://github.com/swagger-api/swagger-ui/archive/refs/tags/v5.11.8.tar.gz | tar xz -C /tmp \
#    && cp -r /tmp/swagger-ui-5.11.8/dist/* /var/www/html/public/swagger/ \
#    && rm -rf /tmp/swagger-ui-5.11.8
# Expose port 80
EXPOSE 80

# Set recommended PHP.ini settings (optionnel, à adapter si besoin)
# COPY docker/web/php.ini /usr/local/etc/php/

# Copy Apache vhost config for public dir
#COPY docker/web/apache.conf /etc/apache2/sites-available/000-default.conf
