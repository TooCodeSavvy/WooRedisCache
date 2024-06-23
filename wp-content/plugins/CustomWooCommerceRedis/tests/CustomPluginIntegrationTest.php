<?php
use PHPUnit\Framework\TestCase;
use CustomWooCommerceRedis\CustomPluginCart;
use CustomWooCommerceRedis\RedisClient;

class CustomPluginIntegrationTest extends TestCase {
    private $redisClient;
    private $customPluginCart;

    protected function setUp(): void {
        // Use real Redis client for integration test
        $this->redisClient = new RedisClient();
        $this->customPluginCart = new CustomPluginCart($this->redisClient);

        // Clean Redis database before running test
        $this->redisClient->flushdb();

        
    }
 
 

    public function testAddToCart() {
        // Voeg een product toe aan de winkelwagen
        WC()->cart->add_to_cart(34, 2);
        WC()->cart->add_to_cart(26, 2); 
 
        // Synchroniseer winkelwagen met Redis
        $this->customPluginCart->syncCartToRedis();

        // Controleer of de winkelwageninformatie in Redis is opgeslagen
        $cartKey = $this->customPluginCart->getCartKey();
        $cartData = $this->redisClient->get($cartKey);


        // Deserialize the cart data from Redis
        $cartData = unserialize($cartData);

        // Debug the cart data retrieved from Redis
        //var_dump($cartData); 
        //var_dump($cartKey);

         // Validatie van winkelwagengegevens in Redis
        $this->assertNotEmpty($cartData, 'De winkelwageninformatie is niet in Redis opgeslagen');
        $this->assertEquals(34, $cartData[array_key_first($cartData)]['product_id']);
        $this->assertEquals(2, $cartData[array_key_first($cartData)]['quantity']);
        $this->assertEquals(26, $cartData[array_keys($cartData)[1]]['product_id']);
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
        $this->customPluginCart->syncCartToRedis();
       

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
        $this->customPluginCart->syncCartToRedis();
    
        $cartKey = $this->customPluginCart->getCartKey();
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
        $this->customPluginCart->syncCartToRedis();

        // Controleer of het product correct is verwijderd uit Redis
        $cartKey = $this->customPluginCart->getCartKey();
        $cartDataBefore = $this->redisClient->get($cartKey);
        $cartDataBefore = $cartDataBefore ? unserialize($cartDataBefore) : [];
                
        //var_dump($cartData);  
    
        // Verwijder het item uit de winkelwagen
        $woocommerce->cart->remove_cart_item($cart_item_key);
    
        // Controleer of het item niet meer in de winkelwagen bestaat
        $cart_items = $woocommerce->cart->get_cart(); 

        $this->assertFalse(isset($cart_items[$cart_item_key]), 'Het product is nog steeds aanwezig in de winkelwagen na verwijdering.');
    
        // Synchroniseer winkelwagen met Redis
        $this->customPluginCart->syncCartToRedis();

        // Haal de bijgewerkte gegevens van de winkelwagen in Redis op
        $cartDataAfter = $this->redisClient->get($cartKey);
        $cartDataAfter = $cartDataAfter ? unserialize($cartDataAfter) : [];
        
        // Controleer of de winkelwagen in Redis nu leeg is
        $this->assertEmpty($cartDataAfter, 'De winkelwagen in Redis bevat nog steeds gegevens na het verwijderen van het product.');

        // Controleer of de winkelwagen in Redis eerder wel gegevens bevatte
        $this->assertNotEmpty($cartDataBefore, 'Vóór het verwijderen bevatte de winkelwagen in Redis geen gegevens.');
    }
    
    public function testAddMultipleCartItems() {
        global $woocommerce;
        
        $woocommerce->cart->empty_cart();
    
        $product_id_1 = 32;
        $quantity_1 = 2;
        $key1 = $woocommerce->cart->add_to_cart($product_id_1, $quantity_1);
        
        $product_id_2 = 33;
        $quantity_2 = 1;
        $key2 = $woocommerce->cart->add_to_cart($product_id_2, $quantity_2);
    
        $this->customPluginCart->syncCartToRedis();

        // Controleer of het item niet meer in de winkelwagen bestaat
        $cart_items = $woocommerce->cart->get_cart(); 

        //var_dump($cart_items[$key1]);

    
        $cartKey = $this->customPluginCart->getCartKey();
        $cartData = $this->redisClient->get($cartKey);
        $cartData = unserialize($cartData);
    
        $this->assertEquals(2, $cart_items[$key1]['quantity'], 'De winkelwagen in Redis bevat niet het juiste aantal items.');
    }
    
    public function testVariableProductCart() {
        global $woocommerce;
        
        $woocommerce->cart->empty_cart();
    
        $product_id = 13; // Assuming 32 is a variable product ID
        $variation_id = 29; // Example variation ID
        $quantity = 2;
        $key1 = $woocommerce->cart->add_to_cart($product_id, $quantity, $variation_id);
    
        //var_dump($key1);

        $this->customPluginCart->syncCartToRedis();
    
        $cartKey = $this->customPluginCart->getCartKey();
        $cartData = $this->redisClient->get($cartKey);
        $cartData = unserialize($cartData);

        // Controleer of het item niet meer in de winkelwagen bestaat
        $cart_items = $woocommerce->cart->get_cart(); 

        //var_dump($cartData);
    
        $this->assertEquals($product_id, $cartData[$key1]['product_id'], 'Het variabele product is niet correct gesynchroniseerd met Redis.');
        $this->assertEquals($variation_id, $cartData[$key1]['variation_id'], 'Het variabele product is niet correct gesynchroniseerd met Redis.');

        $this->assertEquals($cart_items[$key1]['product_id'], $cartData[$key1]['product_id'], 'Het variabele product is niet correct gesynchroniseerd met Redis.');
        $this->assertEquals($cart_items[$key1]['variation_id'], $cartData[$key1]['variation_id'], 'Het variabele product is niet correct gesynchroniseerd met Redis.');

    }

    public function testPartialRemoveCartItem() {
        global $woocommerce;
        
        $woocommerce->cart->empty_cart();
    
        $product_id_1 = 32;
        $quantity_1 = 2;
        $cart_item_key_1 = $woocommerce->cart->add_to_cart($product_id_1, $quantity_1);
        
        $product_id_2 = 33;
        $quantity_2 = 1;
        $cart_item_key_2 = $woocommerce->cart->add_to_cart($product_id_2, $quantity_2);
    
        $this->customPluginCart->syncCartToRedis();
    
        $woocommerce->cart->remove_cart_item($cart_item_key_2);
        
        $this->customPluginCart->syncCartToRedis();

        // Controleer of het item niet meer in de winkelwagen bestaat
        $cart_items = $woocommerce->cart->get_cart(); 
    
        $cartKey = $this->customPluginCart->getCartKey();
        $cartData = $this->redisClient->get($cartKey);
        $cartData = unserialize($cartData); 
    
        $this->assertFalse(isset($cartData[$cart_item_key_2]), 'Het item is nog steeds aanwezig in de winkelwagen in Redis na gedeeltelijke verwijdering.');
        $this->assertArrayHasKey($cart_item_key_1, $cartData, 'Het overgebleven item is niet correct gesynchroniseerd met Redis.');

        $this->assertFalse(isset($cart_items[$cart_item_key_2]), 'Het item is nog steeds aanwezig in de winkelwagen in Redis na gedeeltelijke verwijdering.');
        $this->assertArrayHasKey($cart_item_key_1, $cart_items, 'Het overgebleven item is niet correct gesynchroniseerd met Redis.');
    }
    
    
}
