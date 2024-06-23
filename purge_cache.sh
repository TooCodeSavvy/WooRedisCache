#!/bin/bash

# Define the cache path
CACHE_PATH="/var/cache/nginx"

# Remove all files in the cache directory
rm -rf ${CACHE_PATH}/*

# Log the action (optional)
echo "Nginx cache purged at $(date)" >> /var/log/nginx/purge_cache.log
