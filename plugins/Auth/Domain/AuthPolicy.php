<?php

declare(strict_types=1);

namespace Keystone\Plugins\Auth\Domain;

use Keystone\Core\Auth\PolicyInterface;
use Keystone\Domain\User\User;

final class AuthPolicy implements PolicyInterface {
    
    public function allows(
        User $user,
        string $ability,
        mixed $resource = null
    ): bool {
        // Admin mag alles
        if ($user->hasRole('admin')) {
            return true;
        }

        return match ($ability) {

            // Admin area betreden
            'access-admin' => false,

            // Andere users beheren
            'manage-users' => false,

            // Eigen account beheren
            'manage-own' => $resource instanceof User
                && $user->id() === $resource->id(),

            default => false,
        };
    }
}
?>