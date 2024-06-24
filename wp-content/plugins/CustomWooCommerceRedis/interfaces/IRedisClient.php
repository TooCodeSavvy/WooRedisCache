<?php
namespace CustomWooCommerceRedis\Interfaces;

if (!interface_exists('CustomWooCommerceRedis\Interfaces\IRedisClient')) {

    interface IRedisClient {
        public function set(string $key, $value, int $expiry): bool;
        public function get(string $key);
        public function delete(string $key): bool;
        public function flushdb(): void;

    }
}