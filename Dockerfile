FROM php:8.2-apache

# Install dependencies dan ekstensi dasar Laravel
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev curl gnupg \
    && docker-php-ext-install pdo mbstring exif pcntl bcmath gd

# Install driver MSSQL (SQLSRV dan PDO_SQLSRV)
RUN curl -sSL https://packages.microsoft.com/keys/microsoft.asc | apt-key add - && \
    curl -sSL https://packages.microsoft.com/config/debian/12/prod.list -o /etc/apt/sources.list.d/mssql-release.list && \
    apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql18 unixodbc-dev && \
    pecl install sqlsrv pdo_sqlsrv && \
    docker-php-ext-enable sqlsrv pdo_sqlsrv

# Aktifkan mod_rewrite
RUN a2enmod rewrite

# Copy source code
COPY . /var/www/html

WORKDIR /var/www/html

# Copy composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Install dependency Laravel
RUN composer install --no-dev --optimize-autoloader

# Permission
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]
