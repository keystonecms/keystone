<?php

declare(strict_types=1);

namespace Keystone\Plugins\Auth\Domain\Auth;

use Keystone\Domain\User\User;
use Keystone\Domain\User\UserRepositoryInterface;
use Keystone\Plugins\Auth\Domain\Token\TokenService;
use Keystone\Plugins\Auth\Domain\Token\TokenType;
use Keystone\Plugins\Auth\Domain\Token\InvalidTokenException;
use Keystone\Plugins\Auth\Infrastructure\Mail\MailerInterface;

final class AuthService {
    public function __construct(
        private UserRepositoryInterface $users,
        private TokenService $tokens,
        private MailerInterface $mailer
    ) {}

    /**
     * Registratie (aanmelden)
     *
     * - maakt user aan met status = pending (of hergebruikt bestaande)
     * - stuurt altijd activatie-mail
     * - lekt niet of email al bestaat
     */
    public function register(string $email): void
    {
        $user = $this->users->findByEmail($email);

        if ($user === null) {
            $user = $this->users->createPending($email);
        }

        // Als user al actief is → niets doen (anti enumeration)
        if ($user->isActive()) {
            return;
        }

        $token = $this->tokens->create(
            $user->id(),
            TokenType::ACTIVATION
        );

        $this->mailer->sendActivation($user, $token);
    }

    /**
     * Account activeren via e-mail link
     *
     * - valideert token
     * - zet user op active
     * - stuurt password-reset mail
     */
    public function activate(string $rawToken): void
    {
        $token = $this->tokens->consume(
            $rawToken,
            TokenType::ACTIVATION
        );

        $this->users->activate($token->userId());

        $user = $this->users->findById($token->userId());

        if ($user === null) {
            // zou nooit mogen gebeuren, maar defensief
            throw new InvalidTokenException();
        }

        $resetToken = $this->tokens->create(
            $user->id(),
            TokenType::PASSWORD_RESET
        );

        $this->mailer->sendPasswordReset($user, $resetToken);
    }

    /**
     * Wachtwoord vergeten
     *
     * - altijd zwijgend
     * - alleen voor actieve users
     */
    public function requestPasswordReset(string $email): void
    {
        $user = $this->users->findByEmail($email);

        if ($user === null || ! $user->isActive()) {
            return;
        }

        $token = $this->tokens->create(
            $user->id(),
            TokenType::PASSWORD_RESET
        );

        $this->mailer->sendPasswordReset($user, $token);
    }

    /**
     * Nieuw wachtwoord instellen
     */
    public function resetPassword(string $rawToken, string $plainPassword): void
    {
        $token = $this->tokens->consume(
            $rawToken,
            TokenType::PASSWORD_RESET
        );

        $hash = password_hash(
            $plainPassword,
            PASSWORD_ARGON2ID
        );

        $this->users->setPassword(
            $token->userId(),
            $hash
        );
    }
}
