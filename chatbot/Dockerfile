FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Copy entire application code first
COPY . .

# Verify Composer installation (optional, for debugging)
RUN composer --version

# Run composer install to install dependencies
RUN composer install --no-scripts

# Use JSON array syntax for CMD
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
