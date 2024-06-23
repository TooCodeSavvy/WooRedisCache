<?php
/*
Plugin Name: Custom WooCommerce Redis Integration
Description: WooCommerce productgegevens en winkelwageninformatie in Redis te cachen.
Version: 1.0
Author: name
*/

// Controleer of WordPress direct toegang probeert te krijgen tot het bestand
defined('ABSPATH') || exit;

// Vereiste bestanden laden
require_once plugin_dir_path(__FILE__) . '/interfaces/IRedisClient.php';
require_once plugin_dir_path(__FILE__) . '/RedisClient.php';
require_once plugin_dir_path(__FILE__) . '/CustomPlugin.php';


// Vereiste bestanden laden met foutafhandeling
function load_custom_plugin_files() {
    $required_files = [
        'interfaces/IRedisClient.php',
        'RedisClient.php',
        'CustomPlugin.php'
    ];

    foreach ($required_files as $file) {
        $path = plugin_dir_path(__FILE__) . $file;
        if (file_exists($path)) {
            require_once $path;
        } else {
            error_log("Required file {$file} not found.");
            return false;
        }
    }

    return true;
}

// Plugin initialisatie
function init_custom_plugin() {
    // Laad vereiste bestanden
    if (!load_custom_plugin_files()) {
        return;
    }

    // Controleer of de benodigde klassen bestaan
    if (!class_exists('CustomWooCommerceRedis\RedisClient') || !class_exists('CustomWooCommerceRedis\CustomPlugin')) {
        return;
    }

    // Instantieer RedisClient en CustomPlugin
    $redisClient = new CustomWooCommerceRedis\RedisClient();
    $GLOBALS['custom_plugin'] = new CustomWooCommerceRedis\CustomPlugin($redisClient);
}

// Hook om de plugin te initialiseren
add_action('plugins_loaded', 'init_custom_plugin');

 
?>
