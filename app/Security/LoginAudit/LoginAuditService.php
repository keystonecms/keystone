<?php

namespace Keystone\Security\LoginAudit;


use Keystone\Security\IpInfo\IpInfoService;
use Keystone\Security\LoginAudit\LoginAuditRepositoryInterface;

final class LoginAuditService {

    public function __construct(
        private IpInfoService $ipInfo,
        private LoginAuditRepositoryInterface $repo
    ) {}

    public function log(int $userId, string $ip): void
    {
        try {
            $info = $this->ipInfo->get($ip);

            $this->repo->store([
                'user_id' => $userId,
                'ip'      => $ip,
                'country' => $info['country'] ?? null,
                'region'  => $info['region'] ?? null,
                'city'    => $info['city'] ?? null,
                'org'     => $info['org'] ?? null,
            ]);
        } catch (\Throwable $e) {
            // 🔑 BELANGRIJK: login mag NOOIT falen hierop
            error_log('Login audit failed: ' . $e->getMessage());
        }
    }
}

?>