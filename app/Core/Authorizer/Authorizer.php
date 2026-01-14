<?php

declare(strict_types=1);

namespace Keystone\Core\Authorizer;

use Keystone\Domain\User\User;
use ReflectionMethod;

final class Authorizer
{
    /** @var array<string, PolicyInterface> */
    private array $policies = [];

    public function registerPolicy(
        string $key,
        PolicyInterface $policy
    ): void {
        $this->policies[$key] = $policy;
    }

    public function allows(
        User $user,
        string $policyKey,
        string $ability,
        mixed $resource = null
    ): bool {
        if (!isset($this->policies[$policyKey])) {
            throw new RuntimeException(
                "Policy not registered: {$policyKey}"
            );
        }

        return $this->policies[$policyKey]
            ->allows($user, $ability, $resource);
    }
}

