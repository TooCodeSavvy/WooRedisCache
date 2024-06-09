# Gebruik de officiële WordPress image als basis
FROM wordpress:latest

# Voeg een aangepaste entrypoint script toe
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Stel de werkmap in naar /var/www/html
WORKDIR /var/www/html

# Kopieer je plugin naar de juiste locatie
#COPY ./wp-content/plugins/CustomWooCommerceRedis /var/www/html/wp-content/plugins/custom-woocommerce-redis-integration

# Optioneel: verander de eigenaar van de bestanden naar www-data
#RUN chown -R www-data:www-data /var/www/html

# Gebruik aangepast entrypoint script
ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
