<?php


namespace Keystone\Core\Auth\TwoFactor;


use Keystone\Core\Auth\TwoFactor\TwoFactorHandlerInterface;
use Keystone\Domain\User\User;

final class NullTwoFactorHandler implements TwoFactorHandlerInterface {
    public function requiresTwoFactor(User $user): bool {
        return false;
    }

    public function start(User $user): ?string {
        return null;
    }
}

?>
