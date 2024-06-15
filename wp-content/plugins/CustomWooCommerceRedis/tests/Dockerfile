# Dockerfile voor PHPUnit-tests
FROM php:8.3-cli

# Installeer vereiste pakketten
RUN apt-get update \
    && apt-get install -y wget unzip

# Installeer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Kopieer de plugin code naar de container
COPY . /var/www/html/wp-content/plugins/custom-woocommerce-redis-integration

# Stel de werkmap in naar de plugin directory
WORKDIR /var/www/html/wp-content/plugins/custom-woocommerce-redis-integration

# Installeer Composer afhankelijkheden
# Als composer.lock niet bestaat, dan zal het installeren alle dependencies ophalen
RUN composer install --no-dev || true

# Voer PHPUnit tests uit
CMD ["vendor/bin/phpunit", "--configuration", "phpunit.xml"]