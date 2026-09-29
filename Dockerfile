FROM php:8.2-apache

RUN docker-php-ext-install mysqli
RUN a2enmod ssl

COPY . /var/www/html/
COPY certs/devticket.crt /etc/ssl/certs/devticket.crt
COPY certs/devticket.key /etc/ssl/private/devticket.key
COPY devticket-ssl.conf /etc/apache2/sites-available/devticket-ssl.conf

RUN a2ensite devticket-ssl

EXPOSE 80 443
