<?php
use PHPUnit\Framework\TestCase;
use CustomWooCommerceRedis\CustomPluginProducts;
use CustomWooCommerceRedis\CustomPlugin;
use CustomWooCommerceRedis\RedisClient;

class CustomPluginIntegrationProductsTest extends TestCase {
    private $redisClient;
    private $customPlugin;
    private $CustomPluginProducts;

    protected function setUp(): void {
        // Use real Redis client for integration test
        $this->redisClient = new RedisClient();
        $this->customPlugin = new CustomPlugin($this->redisClient);

        // Directly call initialize to ensure customPluginCart is initialized
        $this->customPlugin->initialize();
        
        $this->CustomPluginProducts = $this->customPlugin->getCustomPluginProducts();
  
        // Clean Redis database before running test
        $this->redisClient->flushdb();
        
    }
 

    public function testIndexProduct() {
        // ID van het bestaande product dat we willen testen
        $productId = 34;
    
        // Simuleer het ophalen van het echte product
        $product = wc_get_product($productId);
    
        // Indexeer het product
        $this->CustomPluginProducts->indexProduct($productId);
    
        // Ophalen van het product uit Redis 
        $cachedData = $this->customPlugin->getRedisClient()->get("product_$productId");
            
        // Assert
        $this->assertIsArray($cachedData, 'De gecachte data is geen array');
        $this->assertEquals($product->get_id(), $cachedData['id']);
        $this->assertEquals($product->get_name(), $cachedData['name']);
        $this->assertEquals($product->get_price(), $cachedData['price']);
        $this->assertEquals($product->get_stock_quantity(), $cachedData['stock']);
    }
    
    public function testIndexNonProductPost() {
        // ID van een bestaand niet-product post type
        $postId = 156;

        // Simuleer het ophalen van de echte post
        $post = get_post($postId);

        // Indexeer de post
        $this->CustomPluginProducts->indexProduct($postId);

        // Ophalen van de post uit Redis
        $cachedData = $this->customPlugin->getRedisClient()->get("product_$postId");

        // var_dump($cachedData);  

        // Assert
        $this->assertEmpty($cachedData['items'], 'Niet-product posts zouden niet gecachet moeten worden');
    }
    
    public function testGetProduct() {
        // ID van het bestaande product dat we willen testen
        $productId = 34;

        // Simuleer het ophalen van het echte product
        $product = wc_get_product($productId);

        // Indexeer het product
        $this->CustomPluginProducts->indexProduct($productId);

        // Ophalen van het product uit de CustomPluginProducts
        $cachedData = $this->CustomPluginProducts->getProduct($productId);

        // Assert
        $this->assertIsArray($cachedData, 'De gecachte data is geen array');
        $this->assertEquals($product->get_id(), $cachedData['id']);
        $this->assertEquals($product->get_name(), $cachedData['name']);
        $this->assertEquals($product->get_price(), $cachedData['price']);
        $this->assertEquals($product->get_stock_quantity(), $cachedData['stock']);
    }
    
    public function testCacheAllProducts() {
        // Cache all products
        $this->CustomPluginProducts->cacheAllProducts();

        // Fetch all products from WooCommerce
        $args = [
            'post_type' => 'product',
            'posts_per_page' => -1
        ];

        $products = get_posts($args);

        // Assert that each product is cached
        foreach ($products as $productPost) {
            $product = wc_get_product($productPost->ID);
            $cachedData = $this->customPlugin->getRedisClient()->get("product_" . $product->get_id());

            $this->assertIsArray($cachedData, 'De gecachte data is geen array');
            $this->assertEquals($product->get_id(), $cachedData['id']);
            $this->assertEquals($product->get_name(), $cachedData['name']);
            $this->assertEquals($product->get_price(), $cachedData['price']);
            $this->assertEquals($product->get_stock_quantity(), $cachedData['stock']);
        }
    }


    
}
