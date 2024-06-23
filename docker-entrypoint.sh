#!/bin/bash
set -e

# Wacht tot de MySQL server klaar is
until mysqladmin ping -h"db" --silent; do
    sleep 1
done

# Importeer het SQL-bestand
mysql -u exampleuser -pexamplepass exampledb < /docker-entrypoint-initdb.d/exampledb.sql

# Voer het oorspronkelijke entrypoint script uit
docker-php-entrypoint "$@"