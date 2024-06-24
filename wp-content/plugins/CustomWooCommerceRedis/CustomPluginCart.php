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
        add_action('wp_loaded', [$this, 'syncCartToRedis']);
    }

    public function getSessionKey() {
        if (function_exists('WC') && WC()->session) {
            // Probeer eerst de sessie-cookie te krijgen
            $session_cookie = WC()->session->get_session_cookie();
            if (is_array($session_cookie) && !empty($session_cookie[0])) {
                return $session_cookie[0];
            } else {
                // Gebruik de get_customer_id() methode van WC_Session_Handler
                $customer_id = WC()->session->get_customer_id();
                if ($customer_id) {
                    return $customer_id;
                }
            }
        }
        return null;
    }
    
    public function getCartKey() {
        $session_key = $this->getSessionKey();
        return $session_key ? 'cart_' . $session_key : null;
    }
    

    public function syncCartToRedis() {
        if (!function_exists('WC') || !WC()->cart) {
            return;
        }

        $cart = WC()->cart->get_cart();
        $cartData = [];
        $subtotal = 0;

        foreach ($cart as $cart_item_key => $cart_item) {
            $product = $cart_item['data'];
            $item_subtotal = $cart_item['quantity'] * $product->get_price();
            $subtotal += $item_subtotal;

            $cartData['items'][$cart_item_key] = [
                'product_id' => $cart_item['product_id'],
                'variation_id' => $cart_item['variation_id'],
                'quantity' => $cart_item['quantity'],
                'name' => $product->get_name(),
                'price' => $product->get_price(),
                'currency' => get_woocommerce_currency_symbol(),
                'image_url' => wp_get_attachment_url($product->get_image_id()),
                'alt_text' => $product->get_title(),
                'product_url' => $product->get_permalink()
            ];
        }

        $cartData['subtotal'] = $subtotal;
        $cartData['cart_count'] = WC()->cart->get_cart_contents_count();

        $cartKey = $this->getCartKey(); 
        if ($cartKey) { 
            if (empty($cartData['items'])) {
                $this->redisClient->delete($cartKey);
            } else {
                $this->redisClient->set($cartKey, $cartData, 3600);
            }
        } else {
            echo "No Cart Key available"; // Debugging
        }
    }
}
?>
