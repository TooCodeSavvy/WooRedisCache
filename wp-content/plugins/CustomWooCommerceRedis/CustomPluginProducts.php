<?php
namespace CustomWooCommerceRedis;

class CustomPluginProducts {
    private $redisClient; 

    public function __construct(Interfaces\IRedisClient $redisClient) {
        $this->redisClient = $redisClient;
        add_action('save_post', [$this, 'indexProduct']);
        add_action('save_post_product', [$this, 'cacheProductData']);
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

    public function cacheProductData($postId) {
        $product = wc_get_product($postId);
        $key = 'product_' . $postId;
        $data = json_encode($product->get_data());
        $this->redisClient->set($key, $data, 3600); // Cache voor 1 uur
    }
    
    public function cacheAllProducts() {
        $args = [
            'post_type' => 'product',
            'posts_per_page' => -1
        ];

        $products = get_posts($args);

        foreach ($products as $productPost) {
            $product = wc_get_product($productPost->ID);
            $productData = [
                'id' => $product->get_id(),
                'name' => $product->get_name(),
                'price' => $product->get_price(),
                'stock' => $product->get_stock_quantity(),
            ];

            $this->redisClient->set("product_" . $product->get_id(), $productData);
        }
    }

}

?>