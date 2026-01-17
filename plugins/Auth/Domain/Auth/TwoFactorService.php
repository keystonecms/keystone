<?php

declare(strict_types=1);

namespace Keystone\Plugins\Auth\Domain\Auth;

use Keystone\Domain\User\User;
use Keystone\Plugins\Auth\Domain\Token\TokenService;
use Keystone\Plugins\Auth\Domain\Token\TokenType;
use Keystone\Plugins\Auth\Domain\Token\InvalidTokenException;
use PragmaRX\Google2FA\Google2FA;
use RuntimeException;

final class TwoFactorService {
    public function __construct(
        private TokenService $tokens,
        private Google2FA $google2fa
    ) {}

    /**
     * Start een 2FA login challenge
     *
     * @return string Raw challenge token (voor URL)
     */
    public function startChallenge(User $user): string
    {
        if (! $user->hasTwoFactor()) {
            throw new RuntimeException('User has no 2FA enabled');
        }


        return $this->tokens->create(
            $user->id(),
            TokenType::TWO_FACTOR
        );
    }

    /**
     * Verifieer 2FA challenge + TOTP code
     *
     * @return int userId (bij succes)
     */
    public function verify(
        string $rawToken,
        string $code,
        string $secret
    ): int {
        // 1. Valideer & consumeer challenge token
        $token = $this->tokens->consume(
            $rawToken,
            TokenType::TWO_FACTOR
        );

        // 2. Valideer TOTP-code
        if (! $this->google2fa->verifyKey($secret, $code)) {
            throw new RuntimeException('Invalid 2FA code');
        }

        // 3. Alles OK → userId terug
        return $token->userId();
    }
}


?>