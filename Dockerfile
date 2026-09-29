FROM php:8.2-apache

RUN apt-get update && apt-get install -y unzip && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli
RUN a2enmod ssl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html/
COPY certs/devticket.crt /etc/ssl/certs/devticket.crt
COPY certs/devticket.key /etc/ssl/private/devticket.key
COPY certs/chain.pem /etc/ssl/certs/chain.pem
COPY devticket-ssl.conf /etc/apache2/sites-available/devticket-ssl.conf

RUN a2ensite devticket-ssl

EXPOSE 80 443
