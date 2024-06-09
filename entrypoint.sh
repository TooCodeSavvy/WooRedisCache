#!/bin/bash
# Aangepast entrypoint script om WordPress-bestanden kopiëren te voorkomen

# Set de startdirectory naar /var/www/html
cd /var/www/html
# Roep het standaard docker-entrypoint.sh script aan
/usr/local/bin/docker-entrypoint.sh "$@"
