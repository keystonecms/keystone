<?php

namespace Keystone\Security\IpInfo;

use Keystone\Security\IpInfo\IpInfoCacheInterface;

final class NullIpInfoCache implements IpInfoCacheInterface {
    public function get(string $ip): ?array { return null; }
    public function set(string $ip, array $data, int $ttl): void {}
}

?>