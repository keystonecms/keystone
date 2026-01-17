<?php

namespace Keystone\Plugins\System\Domain;

use Keystone\Core\Auth\PolicyInterface;
use Keystone\Domain\User\User;

final class UpdatePolicy implements PolicyInterface {
    
    public function allows(User $user, string $ability, mixed $resource = null): bool
    {
        return $user->hasRole('admin');
    }
}

?>