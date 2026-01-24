<?php

namespace Keystone\Core\User;

final class UserSecuritySettingsDto {
    public function __construct(
        private bool $notifyNewIp,
        private bool $notifyFailedLogins,
    ) {}

    public function notifyNewIp(): bool
    {
        return $this->notifyNewIp;
    }

    public function notifyFailedLogins(): bool
    {
        return $this->notifyFailedLogins;
    }
}
?>