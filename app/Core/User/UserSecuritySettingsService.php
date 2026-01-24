<?php

namespace Keystone\Core\User;

final class UserSecuritySettingsService {
    public function __construct(
        private UserSecuritySettingsRepository $repository,
    ) {}

    public function get(int $userId): UserSecuritySettingsDto
    {
        return $this->repository->getForUser($userId);
    }

    public function update(
        int $userId,
        bool $notifyNewIp,
        bool $notifyFailedLogins
    ): void {
        $this->repository->saveForUser(
            $userId,
            $notifyNewIp,
            $notifyFailedLogins
        );
    }
}

?>