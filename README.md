# Custom WooCommerce Redis Integration

Een aangepaste WordPress plugin om WooCommerce productgegevens en winkelwageninformatie in Redis te cachen.

## Beschrijving

Deze plugin integreert Redis caching in WooCommerce om de prestaties te verbeteren door productgegevens en winkelwageninformatie op te slaan in een Redis database. Dit kan helpen bij het sneller laden van productpagina's en het efficiënter beheren van winkelwagens.

## Functies

- Caching van WooCommerce productgegevens in Redis
- Caching van winkelwageninformatie in Redis
- Automatische invalidatie van cache bij productupdates
- Ondersteunt Redis als caching backend via Predis

## Installatie


1. **Installeer de vereisten via Composer**:
    ```bash
    composer install
    ```

2. **Activeer de plugin in WordPress**:
    - Upload de plugin directory naar de `/wp-content/plugins/` directory.
    - Activeer de plugin via het 'Plugins' menu in WordPress.

### Bestandsstructuur

- `src/interfaces/IRedisClient.php` - Interface voor de Redis client.
- `src/RedisClient.php` - Implementatie van de Redis client.
- `src/CustomPlugin.php` - Hoofdplugin klasse.
- `src/custom-woocommerce-redis-integration.php` - Plugin bootstrap bestand.

### Composer

Deze plugin gebruikt Composer voor afhankelijkheden. Zorg ervoor dat je Composer hebt geïnstalleerd en voer `composer install` uit om de vereisten te installeren.

