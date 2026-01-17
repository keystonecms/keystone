<?php

namespace Keystone\Domain\User;

use Keystone\Infrastructure\Auth\PasswordHasher;
use RuntimeException;

final class UserService
{
    public function __construct(
        private UserRepositoryInterface $users,
        private PasswordHasher $hasher
    ) {}

    public function authenticate(string $email, string $password): User
    {
        $user = $this->users->findByEmail($email);

        if (! $user->isActive()) {
                 throw new RuntimeException('User not active');
        }

        if (!$user || !$user->isActive()) {
            throw new RuntimeException('Invalid credentials');
        }

        if (!$this->hasher->verify($password, $user->passwordHash())) {
            throw new RuntimeException('Invalid credentials');
        }

        return $user;
    }

    public function all(): array {
    return $this->users->findAll();
    }

    public function changeStatus(int $userId, string $status): void {
    if (!in_array($status, ['pending', 'active', 'disabled'], true)) {
        throw new \InvalidArgumentException('Invalid status');
    }

    $this->users->updateStatus($userId, $status);
}

public function changePassword(
    int $userId,
    string $current,
    string $new
): void {
    $user = $this->users->findById($userId);

    if (!password_verify($current, $user->passwordHash())) {
        throw new \RuntimeException('Invalid password');
    }

    $hash = password_hash($new, PASSWORD_DEFAULT);
    $this->users->updatePassword($userId, $hash);
}


public function setTwoFactorSecret(int $userId, ?string $secret): void {
    $this->users->setTwoFactorSecret($userId, $secret);
}


    public function create(
        string $email,
        string $plainPassword,
        array $roles = ['admin']
    ): User {
        $hash = $this->hasher->hash($plainPassword);

        $user = new User(
            id: 0,
            email: $email,
            passwordHash: $hash,
            status: 'active',
            roles: $roles
        );

        $this->users->save($user);

        return $user;
    }
}
?>