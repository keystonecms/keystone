<?php

namespace Keystone\Core\Auth;

use Keystone\Domain\User\UserRepositoryInterface;

final class PolicyResolver
{
    public function __construct(

        private UserRepositoryInterface $users,
    ) {}

    public function userHasPolicy(int $userId, string $policy): bool {
        $policies = $this->users->policyKeys($userId);

        // wildcard: admin
        if (in_array('*', $policies, true)) {
            return true;
        }

        return in_array($policy, $policies, true);
    }
}


?>
