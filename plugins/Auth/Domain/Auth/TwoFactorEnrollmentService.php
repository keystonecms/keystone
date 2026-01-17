<?php

namespace Keystone\Plugins\Auth\Domain\Auth;

use Keystone\Domain\User\User;
use Keystone\Domain\User\UserService;
use PragmaRX\Google2FA\Google2FA;
use Keystone\Domain\User\CurrentUser;

final class TwoFactorEnrollmentService {
    public function __construct(
        private CurrentUser $currentUser,
        private UserService $users,
        private Google2FA $google2fa
    ) {}

    public function start(User $user): string
    {
        return $this->google2fa->generateSecretKey();
    }

    public function provisioningUri(User $user, string $secret): string {
   
    return $this->google2fa->getQRCodeUrl(
        'Keystone CMS',           // issuer
        $this->currentUser->user()->email(),           // account name
        $secret
    );
}


    public function verifyAndEnable(
        User $user,
        string $secret,
        string $code
    ): void {
        if (! $this->google2fa->verifyKey($secret, $code)) {
            throw new \RuntimeException('Invalid 2FA code');
        }

        $this->users->setTwoFactorSecret(
            $user->id(),
            $secret
        );
    }
}


?>