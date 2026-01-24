<?php

namespace Keystone\Core\Security;

use Keystone\Core\Security\SecurityEventRepository;
use Keystone\Core\Security\SecurityNotificationService;

final class SecurityEventService {

    public function __construct(
        private SecurityEventRepository $repository,
        private SecurityNotificationService $notifier,
    ) {}

    public function record(
        int $userId,
        string $type,
        string $ip
    ): void {
        $this->repository->insert($userId, $type, $ip);

        if (in_array($type, [
            'login_new_ip',
            'login_failed_threshold',
        ], true)) {
            $this->notifier->notify($userId, $type, $ip);
        }
    }

    public function isNewIp(int $userId, string $ip): bool
    {
        return !$this->repository->hasSeenIpBefore($userId, $ip);
    }

    public function failedAttemptsExceeded(
        int $userId,
        int $threshold,
        int $minutes
    ): bool {
        return $this->repository
                ->failedAttemptsLastMinutes($userId, $minutes)
            >= $threshold;
    }
}

?>