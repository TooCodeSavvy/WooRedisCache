<?php
/*
Plugin Name: Custom WooCommerce Redis Integration
Description: Een aangepaste plugin om WooCommerce productgegevens en winkelwageninformatie in Redis te cachen.
Version: 1.0
Author: A
*/

require_once plugin_dir_path(__FILE__) . '/interfaces/IRedisClient.php';
require_once plugin_dir_path(__FILE__) . '/RedisClient.php';
require_once plugin_dir_path(__FILE__) . '/CustomPlugin.php';

function init_custom_plugin() {
    $redisClient = new CustomWooCommerceRedis\RedisClient();
    $customPlugin = new CustomWooCommerceRedis\CustomPlugin($redisClient);
    return $customPlugin;
}

add_action('plugins_loaded', 'init_custom_plugin');
?>
