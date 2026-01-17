<?php

declare(strict_types=1);

namespace Keystone\Infrastructure\Persistence;

use Keystone\Domain\User\User;
use Keystone\Domain\User\UserRepositoryInterface;
use PDO;
use Keystone\Core\Auth\UserStatus;

final class UserRepository implements UserRepositoryInterface {
    public function __construct(
        private PDO $pdo
    ) {}

public function updateStatus(int $id, string $status): void
{
    $stmt = $this->pdo->prepare(
        'UPDATE users SET status = :status WHERE id = :id'
    );

    $stmt->execute([
        'status' => $status,
        'id'     => $id,
    ]);
}

public function updatePassword(int $id, string $hash): void
{
    $stmt = $this->pdo->prepare(
        'UPDATE users SET password_hash = :hash WHERE id = :id'
    );

    $stmt->execute([
        'hash' => $hash,
        'id'   => $id,
    ]);
}


public function setTwoFactorSecret(int $userId, ?string $secret): void
{
    $stmt = $this->pdo->prepare(
        'UPDATE users SET two_factor_secret = :secret WHERE id = :id'
    );

    $stmt->execute([
        'secret' => $secret,
        'id' => $userId,
    ]);
}


public function createPending(string $email): User
{
    $stmt = $this->pdo->prepare(
        'INSERT INTO users (email, password_hash, status, roles)
         VALUES (:email, NULL, :status, :roles)'
    );

    $stmt->execute([
        'email'  => $email,
        'status' => 'pending',
        'roles'  => json_encode(['user']),
    ]);

    return new User(
        id: (int) $this->pdo->lastInsertId(),
        email: $email,
        passwordHash: null,
        status: 'pending',
        roles: ['user']
    );
}


public function activate(int $userId): void
{
    $stmt = $this->pdo->prepare(
        'UPDATE users SET status = :status WHERE id = :id'
    );

    $stmt->execute([
        'status' => 'active',
        'id'     => $userId,
    ]);
}


    public function setPassword(int $userId, string $hash): void {
        $stmt = $this->pdo->prepare(
            'UPDATE users SET password_hash = :hash WHERE id = :id'
        );

        $stmt->execute([
            'hash' => $hash,
            'id'   => $userId,
        ]);
    }

public function findAll(): array
{
    $stmt = $this->pdo->query(
        'SELECT * FROM users ORDER BY email'
    );

    $rows = $stmt->fetchAll();

    return array_map(
        fn (array $row) => $this->map($row),
        $rows
    );
}
public function findById(int $id): ?User {
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

    private function map(array $row): User {
       return new User(
            id: (int) $row['id'],
            email: $row['email'],
            passwordHash: $row['password_hash'],
            status: $row['status'], 
            roles: json_decode($row['roles'], true),
            twoFactorSecret: $row['two_factor_secret']
        );
    }
}


?>