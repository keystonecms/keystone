<?php

declare(strict_types=1);

namespace Keystone\Plugins\Pages\Domain;

use Keystone\Core\Auth\PolicyInterface;
use Keystone\Domain\User\User;

final class PagePolicy implements PolicyInterface
{
    public function allows(
        User $user,
        string $ability,
        mixed $resource = null
    ): bool {
        // MVP: admin mag alles
        if ($user->hasRole('admin')) {
            return true;
        }

        return match ($ability) {
            'view' => true,
            'edit', 'create', 'publish', 'delete' => false,
            default => false,
        };
    }
}

?>
