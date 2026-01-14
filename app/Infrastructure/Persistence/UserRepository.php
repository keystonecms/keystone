<?php

declare(strict_types=1);

namespace Keystone\Infrastructure\Persistence;

use Keystone\Domain\User\User;
use Keystone\Domain\User\UserRepositoryInterface;
use PDO;

final class UserRepository implements UserRepositoryInterface {
    public function __construct(
        private PDO $pdo
    ) {}

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        return $row ? $this->map($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);

        $row = $stmt->fetch();

        return $row ? $this->map($row) : null;
    }

    public function save(User $user): void
    {
        // later (niet nodig voor login)
    }

    private function map(array $row): User
    {
        return new User(
            id: (int) $row['id'],
            email: $row['email'],
            passwordHash: $row['password_hash'],
            active: (bool) $row['active'],
            roles: json_decode($row['roles'], true)
        );
    }
}


?>