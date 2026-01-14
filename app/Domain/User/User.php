<?php

namespace Keystone\Domain\User;

final class User {
    public function __construct(
        private int $id,
        private string $email,
        private string $passwordHash,
        private bool $active,
        private array $roles = []
    ) {}

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

    public function isActive(): bool
    {
        return $this->active;
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
