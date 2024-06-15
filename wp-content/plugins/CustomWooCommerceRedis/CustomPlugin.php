<?php
namespace CustomWooCommerceRedis;

class CustomPlugin {
    private $customPluginProducts;
    private $customPluginCart;

    public function __construct() {
        add_action('init', [$this, 'initialize']);
    }

    public function initialize() {
        $redisClient = new RedisClient(); // Assuming you have a RedisClient class implementing IRedisClient

        $this->customPluginProducts = new CustomPluginProducts($redisClient);
        $this->customPluginCart = new CustomPluginCart($redisClient);
    }
}

?>