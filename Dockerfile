# Gebruik de officiële WordPress image als basis
FROM wordpress:latest

# Installeer vereiste pakketten
RUN apt-get update && apt-get install -y wget unzip

# Installeer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Kopieer de plugin code naar de container
COPY ./wp-content/plugins/CustomWooCommerceRedis /var/www/html/wp-content/plugins/custom-woocommerce-redis-integration

# Kopieer Composer-bestanden naar de container
COPY composer.json /var/www/html/wp-content/plugins/custom-woocommerce-redis-integration/composer.json
#COPY composer.lock /var/www/html/wp-content/plugins/custom-woocommerce-redis-integration/composer.lock

# Installeer Composer afhankelijkheden
WORKDIR /var/www/html/wp-content/plugins/custom-woocommerce-redis-integration
RUN composer install --no-dev

# Kopieer het set-permissions.sh script naar de container
COPY set-permissions.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/set-permissions.sh

# Stel de werkmap in naar /var/www/html
WORKDIR /var/www/html

# Download en pak WooCommerce uit
RUN wget https://downloads.wordpress.org/plugin/woocommerce.zip \
    && unzip woocommerce.zip -d /var/www/html/wp-content/plugins \
    && rm woocommerce.zip

# CMD voor het uitvoeren van het set-permissions.sh script bij het starten van de container
CMD ["/usr/local/bin/set-permissions.sh"]
