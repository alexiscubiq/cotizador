FROM php:8.3-fpm

# Instalar dependencias del sistema y Node.js
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    nginx \
    sqlite3 \
    libsqlite3-dev \
    nodejs \
    npm

# Limpiar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP
RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip intl

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar archivos composer primero para cache de Docker
COPY composer.json composer.lock ./

# Instalar dependencias de Composer
RUN composer install --no-dev --no-scripts --optimize-autoloader

# Copiar package.json para cache de Docker
COPY package.json package-lock.json* ./

# Instalar dependencias de Node.js
RUN npm install

# Copiar el resto de archivos de la aplicación
COPY . .

# Compilar assets de Vite para producción
RUN npm run build

# Configurar nginx
COPY nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Configurar PHP-FPM
COPY php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Copiar y dar permisos al script de inicio
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Cambiar permisos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/database \
    && touch /var/www/database/database.sqlite \
    && chmod 664 /var/www/database/database.sqlite

# Exponer puerto
EXPOSE 8080

# Comando de inicio
CMD ["/usr/local/bin/docker-entrypoint.sh"]
