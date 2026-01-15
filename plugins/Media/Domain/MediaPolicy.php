<?php

namespace Keystone\Plugins\Media\Domain;

use Keystone\Core\Auth\PolicyInterface;
use Keystone\Domain\User\User;

final class MediaPolicy implements PolicyInterface {
  

public function upload(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }


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