<?php

declare(strict_types=1);

namespace Keystone\Domain\User;

interface UserRepositoryInterface
{

    public function setTwoFactorSecret(int $userId, ?string $secret): void;

    public function findById(int $id): ?User;

    public function findAll(): array;

    public function findByEmail(string $email): ?User;

    public function createPending(string $email): User;

     public function updateStatus(int $id, string $status): void;

    public function activate(int $userId): void;

    public function setPassword(int $userId, string $hash): void;
}

?>
