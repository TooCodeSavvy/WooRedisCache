<?php
namespace CustomWooCommerceRedis\Interfaces;

interface IRedisClient {
    public function set(string $key, $value, int $expiry): bool;
    public function get(string $key);
    public function delete(string $key): bool;
}
