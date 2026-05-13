FROM php:8.2-apache

# Instalar extensiones necesarias para PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql pdo_pgsql

# Copiar proyecto
COPY . /var/www/html/

# Permisos (opcional pero útil)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80