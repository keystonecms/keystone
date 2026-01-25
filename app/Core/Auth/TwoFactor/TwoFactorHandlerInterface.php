<?php

namespace Keystone\Core\Auth\TwoFactor;

use Keystone\Domain\User\User;

interface TwoFactorHandlerInterface {

    public function requiresTwoFactor(User $user): bool;

    public function start(User $user): ?string;
}


?>
