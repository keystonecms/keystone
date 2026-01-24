<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\Step;

use App\Core\Setup\InstallerState;
use App\Infrastructure\Security\PasswordHasher;
use App\Infrastructure\User\UserRepository;

final class AdminUserStep implements InstallerStep
{
    public function __construct(
        private UserRepository $users,
        private PasswordHasher $hasher,
    ) {}

    public function run(InstallerState $state): void
    {
        if (!$state->adminEmail || !$state->adminPassword) {
            return;
        }

        $this->users->createAdmin(
            $state->adminEmail,
            $this->hasher->hash($state->adminPassword)
        );
    }
}


?>