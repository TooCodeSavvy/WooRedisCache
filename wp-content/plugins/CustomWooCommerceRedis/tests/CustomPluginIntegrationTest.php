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

    public function testAddToCart() {
        // Voeg een product toe aan de winkelwagen
        WC()->cart->add_to_cart(32, 2);
        WC()->cart->add_to_cart(31, 2);
 
        // Synchroniseer winkelwagen met Redis
        $this->customPlugin->syncCartToRedis();

        // Controleer of de winkelwageninformatie in Redis is opgeslagen
        // Controleer of de winkelwageninformatie in Redis is opgeslagen
        $cartKey = $this->customPlugin->getCartKey();
        $cartData = $this->redisClient->get($cartKey);


        // Deserialize the cart data from Redis
        $cartData = unserialize($cartData);

        // Debug the cart data retrieved from Redis
        var_dump($cartData); 
         // Validatie van winkelwagengegevens in Redis
        $this->assertNotEmpty($cartData, 'De winkelwageninformatie is niet in Redis opgeslagen');
        $this->assertEquals(32, $cartData[array_key_first($cartData)]['product_id']);
        $this->assertEquals(2, $cartData[array_key_first($cartData)]['quantity']);
        $this->assertEquals(31, $cartData[array_keys($cartData)[1]]['product_id']);
        $this->assertEquals(2, $cartData[array_keys($cartData)[1]]['quantity']);
    }
}
