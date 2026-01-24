<?php

namespace Keystone\Core\Auth;

use Keystone\Domain\User\User;
use Keystone\Domain\User\CurrentUser;
use Keystone\Domain\User\UserRepositoryInterface;
use Keystone\Http\Session\SessionInterface;

final class AuthService {

    private const SESSION_USER_ID = 'user_id';
    private const SESSION_2FA_ID   = '2fa_user_id';

    public function __construct(
        private SessionInterface $session,
        private UserRepositoryInterface $users,
        private CurrentUser $currentUser,
    ) {}

    /**
     * Wordt 1x per request aangeroepen
     */
    public function boot(): void
    {
        if ($this->session->has(self::SESSION_USER_ID)) {
            $user = $this->users->findById(
                $this->session->get(self::SESSION_USER_ID)
            );
            $this->currentUser->set($user);
        }
    }

    /**
     * Definitieve login (zonder 2FA of na 2FA)
     */
    public function login(User $user): void
    {
        $this->session->regenerate();
        $this->session->set(self::SESSION_USER_ID, $user->id());
        $this->session->remove(self::SESSION_2FA_ID);

        $this->currentUser->set($user);
    }

    /**
     * Start 2FA flow
     */
    public function startTwoFactor(User $user): void {
        $this->session->regenerate();
        $this->session->set(self::SESSION_2FA_ID, $user->id());
    }

    /**
     * Afronden van 2FA
     */
    public function completeTwoFactor(string $code): User {

        if (!$this->session->has(self::SESSION_2FA_ID)) {
            throw new \RuntimeException('No active 2FA session');
        }

        $userId = $this->session->get(self::SESSION_2FA_ID);
        $user   = $this->users->get($userId);

        if (!$user->verifyTwoFactorCode($code)) {
            throw new InvalidTwoFactorCodeException();
        }

        $this->login($user);

        return $user;
    }

    public function logout(): void {
        $this->session->destroy();
    }

    public function isTwoFactorPending(): bool {
        return $this->session->has(self::SESSION_2FA_ID);
    }

    public function user(): ?User {
        return $this->currentUser->user();
    }

    public function check(): bool {
        return $this->currentUser->isAuthenticated();
    }
}

?>