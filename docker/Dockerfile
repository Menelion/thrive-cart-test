FROM php:8.4-apache

RUN a2enmod rewrite headers
RUN docker-php-ext-install pdo_mysql
RUN apt-get update && apt-get install -y net-tools
RUN echo "Listen 0.0.0.0:80" > /etc/apache2/ports.conf && \
    sed -i '/Listen \[::\]:80/d' /etc/apache2/ports.conf

CMD ["apache2-foreground"]
