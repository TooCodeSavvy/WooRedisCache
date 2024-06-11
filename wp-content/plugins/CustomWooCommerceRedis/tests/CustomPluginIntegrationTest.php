<?php
use PHPUnit\Framework\TestCase;
use CustomWooCommerceRedis\CustomPlugin;
use CustomWooCommerceRedis\RedisClient;

class CustomPluginIntegrationTest extends TestCase {
    private $redisClient;
    private $customPlugin;

    protected function setUp(): void {
        // Use real Redis client for integration test
        $this->redisClient = new RedisClient();
        $this->customPlugin = new CustomPlugin($this->redisClient);

        // Clean Redis database before running test
        $this->redisClient->flushdb();
    }

    public function testIndexProduct() {
        // ID van het bestaande product dat we willen testen
        $productId = 34;
    
        // Simuleer het ophalen van het echte product
        $product = wc_get_product($productId);
    
        // Indexeer het product
        $this->customPlugin->indexProduct($productId);
    
        // Ophalen van het product uit Redis
        $cachedData = $this->redisClient->get("product_$productId");
    
        // Assert
        $this->assertIsArray($cachedData, 'De gecachte data is geen array');
        $this->assertEquals($product->get_id(), $cachedData['id']);
        $this->assertEquals($product->get_name(), $cachedData['name']);
        $this->assertEquals($product->get_price(), $cachedData['price']);
        $this->assertEquals($product->get_stock_quantity(), $cachedData['stock']);
    }
    
}
