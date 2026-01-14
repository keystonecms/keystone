<?php

declare(strict_types=1);

namespace Keystone\Core\Authorizer;

use InvalidArgumentException;
use ReflectionMethod;

final class PolicyResolver
{
    /**
     * @var array<string, class-string>
     */
    private array $policies = [];

    public function register(string $ability, string $policyClass): void
    {
        $this->policies[$ability] = $policyClass;
    }

    public function resolve(string $ability): array
    {
        if (!isset($this->policies[$ability])) {
            throw new InvalidArgumentException(
                "No policy registered for ability [$ability]"
            );
        }

        $policyClass = $this->policies[$ability];

        [$resource, $action] = explode('.', $ability, 2);

        return [$policyClass, $action];
    }
}
