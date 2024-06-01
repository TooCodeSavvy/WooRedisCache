<?php
/*
Plugin Name: Custom WooCommerce Redis Integration
Description: Een aangepaste plugin om WooCommerce productgegevens en winkelwageninformatie in Redis te cachen.
Version: 1.0
Author: Anouar
*/

require_once 'IRedisClient.php';
require_once 'RedisClient.php';
require_once 'CustomPlugin.php';

function init_custom_plugin() {
    $redisClient = new RedisClient();
    $customPlugin = new CustomPlugin($redisClient);
}

add_action('plugins_loaded', 'init_custom_plugin');
?>
