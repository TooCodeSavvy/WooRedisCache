<?php
namespace CustomWooCommerceRedis;

if (!class_exists('CustomWooCommerceRedis\CustomPlugin')) {

    class CustomPlugin {
        private $customPluginProducts;
        private $customPluginCart;
        private $redisClient;

        public function __construct(RedisClient $redisClient) {
            $this->redisClient = $redisClient;
            add_action('init', [$this, 'initialize']);
        }

        public function initialize() {
            $this->customPluginProducts = new CustomPluginProducts($this->redisClient);
            $this->customPluginCart = new CustomPluginCart($this->redisClient);
        }

        // Getter voor customPluginCart
        public function getCustomPluginCart() {
            return $this->customPluginCart;
        }

        // Getter voor customPluginProducts
        public function getCustomPluginProducts() {
            return $this->customPluginProducts;
        }
        // Getter voor customPluginProducts
        public function getRedisClient() {
            return $this->redisClient;
        }
    }
}

?>