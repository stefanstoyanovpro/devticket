FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Copy app files into the container's web root
COPY . /var/www/html/

# Apache's default document root is already /var/www/html, so nothing else needed
EXPOSE 80
