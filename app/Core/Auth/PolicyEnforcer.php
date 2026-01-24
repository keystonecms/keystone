<?php

namespace Keystone\Core\Auth;

use Keystone\Core\User\User;

final class PolicyEnforcer {
    
    public function __construct(
        private AuthorityActivityService $activity,
    ) {}

    public function enforce(
        string $policy,
        User $user,
        bool $allowed,
    ): void {
        if ($allowed) {
            return;
        }

        $this->activity->denied(
            policy: $policy,
            userId: $user->id()
        );

        throw new ForbiddenException();
    }
}


?>