<?php

declare(strict_types=1);

namespace Keystone\Core\Auth;

use Keystone\Domain\User\User;

interface PolicyInterface
{
    /**
     * @param string $ability  bv. "view", "edit", "publish"
     * @param mixed|null $resource bv. Page, Post, etc
     */
    public function allows(
        User $user,
        string $ability,
        mixed $resource = null
    ): bool;
}
