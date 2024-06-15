<?php
/*
Plugin Name: Custom WooCommerce Redis Integration
Description: Een aangepaste plugin om WooCommerce productgegevens en winkelwageninformatie in Redis te cachen.
Version: 1.0
Author: Jouw Naam
*/

// Controleer of WordPress direct toegang probeert te krijgen tot het bestand
defined('ABSPATH') || exit;

// Vereiste bestanden laden
require_once plugin_dir_path(__FILE__) . '/interfaces/IRedisClient.php';
require_once plugin_dir_path(__FILE__) . '/RedisClient.php';
require_once plugin_dir_path(__FILE__) . '/CustomPlugin.php';

// Plugin initialisatie
function init_custom_plugin() {
    // Foutafhandeling toevoegen voor het laden van vereiste bestanden
    if (!class_exists('CustomWooCommerceRedis\RedisClient') || !class_exists('CustomWooCommerceRedis\CustomPlugin')) {
        return;
    }

    // Instantieer RedisClient en CustomPlugin
    $redisClient = new CustomWooCommerceRedis\RedisClient();
    $customPlugin = new CustomWooCommerceRedis\CustomPlugin($redisClient);
    
    // Geef de plugin-instantie terug voor eventueel gebruik
    return $customPlugin;
}

// Plugin initialisatie hook
add_action('plugins_loaded', 'init_custom_plugin');