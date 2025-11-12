FROM php:8.2-apache

# Install dependencies dan ekstensi dasar Laravel
RUN apt-get update && apt-get install -y \
    git zip unzip curl gnupg \
    libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev libonig-dev \
    && docker-php-ext-install pdo mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*


# Install driver MSSQL (SQLSRV dan PDO_SQLSRV)
RUN curl -sSL https://packages.microsoft.com/keys/microsoft.asc -o microsoft.asc && \
    gpg --dearmor microsoft.asc && \
    mv microsoft.asc.gpg /etc/apt/trusted.gpg.d/microsoft.gpg && \
    curl -sSL https://packages.microsoft.com/config/debian/12/prod.list -o /etc/apt/sources.list.d/mssql-release.list && \
    apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql18 unixodbc-dev && \
    pecl install sqlsrv pdo_sqlsrv && \
    docker-php-ext-enable sqlsrv pdo_sqlsrv


# Aktifkan mod_rewrite
RUN a2enmod rewrite

# Ubah DocumentRoot ke folder public Laravel
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Copy source code
COPY . /var/www/html

WORKDIR /var/www/html

# Copy composer dari image composer resmi
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Install dependency Laravel
RUN composer install --no-dev --optimize-autoloader

# Permission untuk storage & cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Gunakan port dari Cloud Run
ENV PORT=8080
RUN sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
RUN sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf

EXPOSE ${PORT}

CMD ["apache2-foreground"]
