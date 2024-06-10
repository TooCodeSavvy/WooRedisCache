#!/bin/bash

# Verander de eigenaar van alle bestanden en mappen naar www-data
chown -R www-data:www-data /var/www/html/wp-content/plugins/custom-woocommerce-redis-integration

# Maak alle bestanden en mappen schrijfbaar voor de eigenaar
chmod -R 755 /var/www/html/wp-content/plugins/custom-woocommerce-redis-integration

# Blijf actief met een oneindige loop om de container draaiend te houden
apache2-foreground