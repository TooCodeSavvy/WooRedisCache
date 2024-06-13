<?php

namespace CustomWooCommerceRedis;

require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

use CustomWooCommerceRedis\Interfaces\IRedisClient;
use Predis\Client;

class RedisClient implements IRedisClient {
    private $redisConnection;

    public function __construct() {
        $this->redisConnection = new Client([
            'scheme' => 'tcp',
            'host'   => 'redis', // Verander naar 'redis' als je de Docker Redis service gebruikt
            'port'   => 6379,
        ]);
    }

    public function set(string $key, $value, int $expiry): bool {
        $response = $this->redisConnection->setex($key, $expiry, serialize($value));
        return $response == 'OK';
    }

    public function get(string $key) { 
        $value = $this->redisConnection->get($key);
        return $value ? unserialize($value) : null;
    }

    public function delete(string $key): bool {
        return $this->redisConnection->del([$key]) > 0;
    }

    public function flushdb(): void {
        $this->redisConnection->flushdb();
    }
}
 