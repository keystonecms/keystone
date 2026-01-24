<?php


namespace Keystone\Security\IpInfo;

final class IpInfoService {

    private const TTL = 604800; // 7 dagen

    public function __construct(
        private IpInfoClient $client,
        private IpInfoCacheInterface $cache
    ) {}

    public function get(string $ip): array {

        if (IpAddress::isPrivate($ip)) {
        return [
            'ip'       => $ip,
            'internal' => true,
        ];
    }

        if ($cached = $this->cache->get($ip)) {
            return $cached;
        }

        $data = $this->client->fetch($ip);

        $this->cache->set($ip, $data, self::TTL);

        return $data;
    }
}
