# Gebruik de officiële WordPress image als basis
FROM wordpress:latest

# Kopieer het set-permissions.sh script naar de container
COPY set-permissions.sh /usr/local/bin/

# Geef uitvoeringsrechten aan het script
RUN chmod +x /usr/local/bin/set-permissions.sh

# Stel de werkmap in naar /var/www/html
WORKDIR /var/www/html

# Download en pak WooCommerce uit
RUN apt-get update && apt-get install -y wget unzip \
    && wget https://downloads.wordpress.org/plugin/woocommerce.zip \
    && unzip woocommerce.zip -d /var/www/html/wp-content/plugins \
    && rm woocommerce.zip

# CMD voor het uitvoeren van het set-permissions.sh script bij het starten van de container
CMD ["/usr/local/bin/set-permissions.sh"]
