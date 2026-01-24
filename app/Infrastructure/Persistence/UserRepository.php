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

public function updateAvatar(int $userId, string $path): void   {
    $stmt = $this->pdo->prepare(
        'UPDATE users
            SET avatar_path = :path
            WHERE id = :id'
    );

    $stmt->execute([
        'path' => $path,
        'id'   => $userId,
    ]);
}

public function countAll(): int {
        $stmt = $this->pdo->query(
            'SELECT COUNT(*) FROM users'
        );

        return (int) $stmt->fetchColumn();
    }

public function updateStatus(int $id, string $status): void {
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


public function syncRoles(int $userId, array $roleIds): void {
    
    $this->pdo->beginTransaction();

    $delete = $this->pdo->prepare(
        'DELETE FROM user_roles WHERE user_id = :user'
    );
    $delete->execute(['user' => $userId]);

    $insert = $this->pdo->prepare(
        'INSERT INTO user_roles (user_id, role_id)
         VALUES (:user, :role)'
    );

    foreach ($roleIds as $roleId) {
        $insert->execute([
            'user' => $userId,
            'role' => (int) $roleId,
        ]);
    }

    $this->pdo->commit();
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

public function policyKeys(int $userId): array {
    $stmt = $this->pdo->prepare(
        'SELECT DISTINCT p.key_name
           FROM policies p
           JOIN role_policies rp ON rp.policy_id = p.id
           JOIN user_roles ur ON ur.role_id = rp.role_id
          WHERE ur.user_id = :user'
    );

    $stmt->execute(['user' => $userId]);

    return $stmt->fetchAll(\PDO::FETCH_COLUMN);
}

public function findAllWithRoles(): array
{
    $stmt = $this->pdo->query(
        'SELECT
            u.*,
            r.id    AS role_id,
            r.name  AS role_name,
            r.label AS role_label
         FROM users u
         LEFT JOIN user_roles ur ON ur.user_id = u.id
         LEFT JOIN roles r ON r.id = ur.role_id
         ORDER BY u.email'
    );

    $rows = $stmt->fetchAll();

    $users = [];

    foreach ($rows as $row) {
        $userId = (int) $row['id'];

        if (!isset($users[$userId])) {
            $users[$userId] = $this->map($row);
            $users[$userId]->roles = [];
        }

        if ($row['role_id']) {
            $users[$userId]->roles[] = [
                'id'    => (int) $row['role_id'],
                'name'  => $row['role_name'],
                'label' => $row['role_label'],
            ];
        }
    }

    return array_values($users);
}


public function findAll(): array {
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

public function roleIds(int $userId): array {
    $stmt = $this->pdo->prepare(
        'SELECT role_id
           FROM user_roles
          WHERE user_id = :user'
    );

    $stmt->execute([
        'user' => $userId,
    ]);

    return array_map(
        'intval',
        $stmt->fetchAll(PDO::FETCH_COLUMN)
    );
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
            name: $row['name'],
            email: $row['email'],
            passwordHash: $row['password_hash'],
            status: $row['status'], 
            twoFactorSecret: $row['two_factor_secret'],
            avatarPath: $row['avatar_path']
        );
    }
}


?>