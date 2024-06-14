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
        $cartKey = $this->customPlugin->getCartKey();
        $cartData = $this->redisClient->get($cartKey);


        // Deserialize the cart data from Redis
        $cartData = unserialize($cartData);

        // Debug the cart data retrieved from Redis
        //var_dump($cartData); 
        //var_dump($cartKey);

         // Validatie van winkelwagengegevens in Redis
        $this->assertNotEmpty($cartData, 'De winkelwageninformatie is niet in Redis opgeslagen');
        $this->assertEquals(32, $cartData[array_key_first($cartData)]['product_id']);
        $this->assertEquals(2, $cartData[array_key_first($cartData)]['quantity']);
        $this->assertEquals(31, $cartData[array_keys($cartData)[1]]['product_id']);
        $this->assertEquals(2, $cartData[array_keys($cartData)[1]]['quantity']);
    }

    public function testUpdateCartItem() {
        global $woocommerce;

        $woocommerce->cart->empty_cart();
    
        // Voeg een product toe aan de winkelwagen
        $product_id = 32;
        $quantity = 2;
        $cart_item_key = $woocommerce->cart->add_to_cart($product_id, $quantity);

       // var_dump($cart_item_key); 


        // Synchroniseer winkelwagen met Redis
        $this->customPlugin->syncCartToRedis();
       

        // Controleer of het item in de winkelwagen bestaat
        if ($woocommerce->cart->get_cart_item($cart_item_key)) {
            // Update de hoeveelheid
            $new_quantity = 5;
            $woocommerce->cart->set_quantity($cart_item_key, $new_quantity);
          //  echo "De hoeveelheid is bijgewerkt naar $new_quantity<br>";
        } else {
            echo "Het winkelwagenitem kon niet worden gevonden.<br>";
        }
 
        // Controleer of het product is toegevoegd aan de winkelwagen
        $cart_items = $woocommerce->cart->get_cart();
 
        // Loop through each cart item
       /* foreach ($cart_items as $cart_item_key => $cart_item) {
            // Get the product object
            $product = $cart_item['data'];

            // Get product details
            $product_name = $product->get_name();
            $product_quantity = $cart_item['quantity'];
            $product_price = $product->get_price();

            // Display the product details
            echo "Product Name: " . $product_name . "<br>";
            echo "Quantity: " . $product_quantity . "<br>";
            echo "Price: $" . $product_price . "<br>";
            echo "-----------------------------------<br>";
        } */
    
        // Synchroniseer winkelwagen met Redis
        $this->customPlugin->syncCartToRedis();
    
        $cartKey = $this->customPlugin->getCartKey();
        $cartData = $this->redisClient->get($cartKey);
        $cartData = unserialize($cartData);
        
        //var_dump($cartData); 
      //  var_dump($cartKey);
      
        // Controleer of de hoeveelheid correct is bijgewerkt
        $this->assertEquals(5, $woocommerce->cart->get_cart_contents_count(), 'Aantal producten in de winkelwagen komt niet overeen na bijwerken van de hoeveelheid.');
        $this->assertEquals(5, $cartData[array_key_first($cartData)]['quantity']);
        $this->assertEquals($woocommerce->cart->get_cart_contents_count(), $cartData[array_key_first($cartData)]['quantity']);

    }

    public function testRemoveCartItem() {
        global $woocommerce;
    
        $woocommerce->cart->empty_cart();
    
        // Voeg een product toe aan de winkelwagen
        $product_id = 32;
        $quantity = 2;
        $cart_item_key = $woocommerce->cart->add_to_cart($product_id, $quantity);
    
        // Synchroniseer winkelwagen met Redis
        $this->customPlugin->syncCartToRedis();

        // Controleer of het product correct is verwijderd uit Redis
        $cartKey = $this->customPlugin->getCartKey();
        $cartDataBefore = $this->redisClient->get($cartKey);
        $cartDataBefore = $cartDataBefore ? unserialize($cartDataBefore) : [];
                
        //var_dump($cartData);  
    
        // Verwijder het item uit de winkelwagen
        $woocommerce->cart->remove_cart_item($cart_item_key);
    
        // Controleer of het item niet meer in de winkelwagen bestaat
        $cart_items = $woocommerce->cart->get_cart(); 

        $this->assertFalse(isset($cart_items[$cart_item_key]), 'Het product is nog steeds aanwezig in de winkelwagen na verwijdering.');
    
        // Synchroniseer winkelwagen met Redis
        $this->customPlugin->syncCartToRedis();

        // Haal de bijgewerkte gegevens van de winkelwagen in Redis op
        $cartDataAfter = $this->redisClient->get($cartKey);
        $cartDataAfter = $cartDataAfter ? unserialize($cartDataAfter) : [];
        
        // Controleer of de winkelwagen in Redis nu leeg is
        $this->assertEmpty($cartDataAfter, 'De winkelwagen in Redis bevat nog steeds gegevens na het verwijderen van het product.');

        // Controleer of de winkelwagen in Redis eerder wel gegevens bevatte
        $this->assertNotEmpty($cartDataBefore, 'Vóór het verwijderen bevatte de winkelwagen in Redis geen gegevens.');
    }
    
    
}
