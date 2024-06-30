# Dockerfile voor PHPUnit-tests
FROM wordpress:php8.3-fpm

# Installeer vereiste pakketten
RUN apt-get update \
    && apt-get install -y wget unzip

# Installeer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installeer WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

# Maak de plugins directory aan
RUN mkdir -p /usr/share/nginx/html/wp-content/plugins

# Download en installeer WooCommerce
RUN wget https://downloads.wordpress.org/plugin/woocommerce.latest-stable.zip \
    && unzip woocommerce.latest-stable.zip -d /usr/share/nginx/html/wp-content/plugins/ \
    && rm woocommerce.latest-stable.zip

# Kopieer de plugin code naar de container
COPY ./wp-content/plugins/CustomWooCommerceRedis /usr/share/nginx/html/wp-content/plugins/custom-woocommerce-redis-integration

# Stel de werkmap in naar de plugin directory
WORKDIR /usr/share/nginx/html/wp-content/plugins/custom-woocommerce-redis-integration

ENV COMPOSER_ALLOW_SUPERUSER=1

# Installeer Composer afhankelijkheden
RUN composer install --no-dev

# Voer PHPUnit tests uit
CMD ["vendor/bin/phpunit", "--configuration", "phpunit.xml"]