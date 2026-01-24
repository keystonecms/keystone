<?php

namespace Keystone\Domain\User;

final class User {

    public function __construct(
        private int $id,
        private string $name,
        private string $email,
        private ?string $passwordHash,
        private $status,
        private ?string $twoFactorSecret,
        private ?string $avatarPath
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

     public function name(): string
    {
        return $this->name;
    }
    
    public function avatarPath(): ?string
    {
        return $this->avatarPath;
    }
    
    public function passwordHash(): string
    {
        return $this->passwordHash;
    }
}

?>
