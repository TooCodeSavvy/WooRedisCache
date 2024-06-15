# Dockerfile voor PHPUnit-tests
FROM php:8.3-cli

# Installeer vereiste pakketten
RUN apt-get update \
    && apt-get install -y wget unzip

# Installeer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Kopieer de plugin code naar de container
COPY ./wp-content/plugins/CustomWooCommerceRedis /var/www/html/wp-content/plugins/custom-woocommerce-redis-integration

# Stel de werkmap in naar de plugin directory
WORKDIR /var/www/html/wp-content/plugins/custom-woocommerce-redis-integration

# Kopieer composer.json en composer.lock
COPY ./wp-content/plugins/CustomWooCommerceRedis/composer.json /var/www/html/wp-content/plugins/custom-woocommerce-redis-integration/composer.json
COPY ./wp-content/plugins/CustomWooCommerceRedis/composer.lock /var/www/html/wp-content/plugins/custom-woocommerce-redis-integration/composer.lock

# Installeer Composer afhankelijkheden
RUN composer install --no-dev

# Voer PHPUnit tests uit
CMD ["vendor/bin/phpunit", "--configuration", "phpunit.xml"]