# Dockerfile voor PHPUnit-tests
FROM wordpress:php8.3-fpm

# Installeer vereiste pakketten
RUN apt-get update \
    && apt-get install -y wget unzip

# Installeer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Kopieer de plugin code naar de container
COPY ./wp-content/plugins/CustomWooCommerceRedis /usr/share/nginx/html/wp-content/plugins/custom-woocommerce-redis-integration

# Stel de werkmap in naar de plugin directory
WORKDIR /usr/share/nginx/html/wp-content/plugins/custom-woocommerce-redis-integration

# Installeer Composer afhankelijkheden
RUN composer install --no-dev

# Voer PHPUnit tests uit
CMD ["vendor/bin/phpunit", "--configuration", "phpunit.xml"]