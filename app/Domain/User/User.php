<?php

namespace Keystone\Domain\User;

// use Keystone\Core\Auth\UserStatus;

final class User {
    public function __construct(
        private int $id,
        private string $email,
        private ?string $passwordHash,
        private $status,
        private array $roles = [],
        private ?string $twoFactorSecret
    ) {}

    public function hasTwoFactor(): bool
    {
        return $this->twoFactorSecret !== null;
    }

    public function twoFactorSecret(): ?string
    {
        return $this->twoFactorSecret;
    }

   public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function status(): string
    {
        return $this->status;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }

   public function roles(): array
    {
        return $this->roles;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }
}
