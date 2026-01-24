<?php


namespace Keystone\Security\IpInfo;

interface IpInfoCacheInterface {

    public function get(string $ip): ?array;

    public function set(string $ip, array $data, int $ttl): void;
}


?>