<?php
// Bootstrapping WordPress
require '/usr/share/nginx/html/wp-load.php';

// Controleer of WooCommerce al is geladen
if (!function_exists('WC')) {
    require '/usr/share/nginx/html/wp-content/plugins/woocommerce/woocommerce.php';
}

// Autoloader for your plugin
require dirname(__DIR__) . '/vendor/autoload.php';

// Include your plugin files
require dirname(__DIR__) . '/interfaces/IRedisClient.php';
require dirname(__DIR__) . '/RedisClient.php';
require dirname(__DIR__) . '/CustomPlugin.php';
