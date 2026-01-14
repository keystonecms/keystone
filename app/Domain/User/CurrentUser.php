<?php

declare(strict_types=1);

namespace Keystone\Domain\User;

final class CurrentUser {
    private ?User $user = null;

    public function set(User $user): void
    {
        $this->user = $user;
    }

    public function user(): ?User
    {
         return $this->user;
    }

    public function isAuthenticated(): bool
    {
        return $this->user !== null;
    }
}
