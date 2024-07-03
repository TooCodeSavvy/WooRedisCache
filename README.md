# WooCommerce Redis Integration

Custom WordPress plugin om WooCommerce productgegevens en winkelwagengegevens in Redis te cachen.

## Beschrijving

Deze plugin integreert Redis-caching in WooCommerce om productgegevens en winkelwageninformatie op te slaan in een Redis-database. De bedoeling is om deze plugin te gebruiken bovenop bestaande PHP-code die hooks bijvoorbeeld gebruikt om de winkelwagen te updaten, op te halen etc. Het is ook bedoeld om productgegevens op te halen, alles gebeurt op PHP-niveau.

## Functies

- Caching van WooCommerce productgegevens in Redis.
- Caching van winkelwageninformatie in Redis.

## Installatie

1. Upload de pluginbestanden naar de `/wp-content/plugins/custom-woocommerce-redis-integration` directory, of installeer de plugin direct via de WordPress plugin-directory.
2. Activeer de plugin via het 'Plugins' menu in WordPress.

## Gebruik

### Basisconfiguratie

Zodra de plugin is geïnstalleerd en geactiveerd, moet je op thema-niveau je code aanpassen om de methodes te gebruiken van deze plugin. Het zal de methodes exposen waardoor je die kan aanroepen op je productpagina en in je hooks in je `functions.php` of je snippets.

### Voorbeeld van het bijwerken van een winkelwagenitem:

```php
public function testUpdateCartItem() {
    global $woocommerce;

    $woocommerce->cart->empty_cart();
    
    // Voeg een product toe aan de winkelwagen
    $product_id = 32;
    $quantity = 2;
    $cart_item_key = $woocommerce->cart->add_to_cart($product_id, $quantity);

    // Synchroniseer winkelwagen met Redis
    $this->customPlugin->syncCartToRedis();

    // Controleer of het item in de winkelwagen bestaat
    if ($woocommerce->cart->get_cart_item($cart_item_key)) {
        // Update de hoeveelheid
        $new_quantity = 5;
        $woocommerce->cart->set_quantity($cart_item_key, $new_quantity);
    }

    // Synchroniseer winkelwagen met Redis
    $this->customPlugin->syncCartToRedis();

    $cartKey = $this->customPluginCart->getCartKey();
    $cartData = $this->customPlugin->getRedisClient()->get($cartKey);

    if (!empty($cartData['items'])) {
        $keys = array_keys($cartData['items']); 
    } else {
        echo "Cart is empty.";
    }

    // Controleer of de hoeveelheid correct is bijgewerkt
    $this->assertEquals(5, $woocommerce->cart->get_cart_contents_count(), 'Aantal producten in de winkelwagen komt niet overeen na bijwerken van de hoeveelheid.');
    $this->assertEquals(5, $cartData['items'][$keys[0]]['quantity']);
    $this->assertEquals($woocommerce->cart->get_cart_contents_count(), $cartData['items'][$keys[0]]['quantity']);
}