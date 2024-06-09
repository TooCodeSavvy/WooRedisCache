<?php

namespace CustomWooCommerceRedis;

require 'vendor/autoload.php';

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
        return $this->redisConnection->setex($key, $expiry, serialize($value));
    }

    public function get(string $key) {
        $value = $this->redisConnection->get($key);
        return $value ? unserialize($value) : null;
    }

    public function delete(string $key): bool {
        return $this->redisConnection->del([$key]) > 0;
    }
}
