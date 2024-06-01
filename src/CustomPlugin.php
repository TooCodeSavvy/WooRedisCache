<?php

class CustomPlugin {
    private $redisClient;

    public function __construct(IRedisClient $redisClient) {
        $this->redisClient = $redisClient;
        add_action('save_post', [$this, 'indexProduct']);
        add_action('woocommerce_add_to_cart', [$this, 'addToCart']);
        add_action('woocommerce_remove_cart_item', [$this, 'removeFromCart']);
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

    public function addToCart($cartItemKey) {
        $cartItem = WC()->cart->get_cart_item($cartItemKey);
        $productId = $cartItem['product_id'];
        $quantity = $cartItem['quantity'];

        $cartData = [
            'product_id' => $productId,
            'quantity' => $quantity,
        ];

        $this->redisClient->set("cart_item_$cartItemKey", $cartData, 3600);
    }

    public function removeFromCart($cartItemKey) {
        $this->redisClient->delete("cart_item_$cartItemKey");
    }
}


?>