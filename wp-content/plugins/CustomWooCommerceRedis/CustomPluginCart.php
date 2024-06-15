<?php
namespace CustomWooCommerceRedis;

class CustomPluginCart {
    private $redisClient; 

    public function __construct(Interfaces\IRedisClient $redisClient) {
        $this->redisClient = $redisClient;
        add_action('woocommerce_add_to_cart', [$this, 'syncCartToRedis']);
        add_action('woocommerce_remove_cart_item', [$this, 'syncCartToRedis']);
        add_action('woocommerce_cart_item_quantity_updated', [$this, 'syncCartToRedis']);
        add_action('woocommerce_cart_item_removed', [$this, 'syncCartToRedis']);
    }

    public function getSessionKey() {
        // Access the protected _cookie property using a getter method
        $session_handler = WC()->session;
        $reflection = new \ReflectionClass($session_handler);
        $property = $reflection->getProperty('_cookie');
        $property->setAccessible(true);
        return $property->getValue($session_handler);
    }

    public function getCartKey() {
        // Use the WooCommerce session key
        $session_key = $this->getSessionKey();
        return $session_key;
    }

    public function syncCartToRedis() {
        // Get cart data from WooCommerce session
        $cartData = WC()->session->get('cart');

        // Check if the cart is empty
        if (empty($cartData)) {
            $cartKey = $this->getCartKey();
            $this->redisClient->delete($cartKey); // Verwijder de cart uit Redis als de winkelwagen leeg is
        } else {
            // Serialize the cart data
            $serializedCartData = serialize($cartData);

            // Get the unique cart key
            $cartKey = $this->getCartKey();

            // Store serialized cart data in Redis with the unique cart key
            $this->redisClient->set($cartKey, $serializedCartData, 3600);
        }
    }
}

?>