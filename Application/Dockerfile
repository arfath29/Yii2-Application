# Use official PHP image with Apache
FROM php:7.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Set DocumentRoot to /var/www/html/web
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/web|' /etc/apache2/sites-available/000-default.conf

# Allow .htaccess overrides
RUN echo '<Directory /var/www/html/web>\n    AllowOverride All\n</Directory>' >> /etc/apache2/apache2.conf

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Install Yii2 dependencies (only once)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Health check
HEALTHCHECK --interval=30s --timeout=3s \
  CMD curl -f http://localhost/ || exit 1

EXPOSE 80
