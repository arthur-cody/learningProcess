# Use the official PHP image as base
FROM php:8.3-fpm

# Set the working directory inside the container
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && \
    apt-get install -y \
        git \
        unzip

# Copy the composer files into the container
COPY . /var/www/html/

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Composer dependencies

RUN composer install

# Copy the rest of the application files
# COPY . /var/www/html/

# Generate autoload files
RUN composer dump-autoload --optimize

# Expose port 9000 to the Docker host
EXPOSE 80

# Start PHP-FPM
CMD ["php-fpm"]
