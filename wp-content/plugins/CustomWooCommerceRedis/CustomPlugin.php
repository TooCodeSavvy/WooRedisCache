<?php
namespace CustomWooCommerceRedis;

class CustomPlugin {
    private $redisClient; 

    public function __construct(Interfaces\IRedisClient $redisClient) {
        $this->redisClient = $redisClient;
        add_action('save_post', [$this, 'indexProduct']);
        add_action('save_post_product', [$this, 'cacheProductData']);

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

     
    public function indexProduct($postId) {
        if (get_post_type($postId) !== 'product') {
            return;
        }

        $product = wc_get_product($postId);
        $productData = [
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'price' => $product->get_price(),
            'stock' => $product->get_stock_quantity(),
        ];

        $this->redisClient->set("product_$postId", $productData, 3600);
    }

    public function getProduct($productId) {
        return $this->redisClient->get("product_$productId");
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
    
    public function syncCartFromRedis() {
        $cartData = $this->redisClient->get($this->getCartKey());

        if ($cartData) {
            WC()->cart->empty_cart();
            foreach ($cartData as $cartItemKey => $cartItem) {
                WC()->cart->add_to_cart($cartItem['product_id'], $cartItem['quantity']);
            }
        }
    }
 

    public function cacheProductData($postId) {
        $product = wc_get_product($postId);
        $key = 'product_' . $postId;
        $data = json_encode($product->get_data());
        $this->redisClient->set($key, $data, 3600); // Cache voor 1 uur
    }
}
?>
