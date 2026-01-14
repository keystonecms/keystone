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

        if (!$user || !$user->isActive()) {
            throw new RuntimeException('Invalid credentials');
        }

        if (!$this->hasher->verify($password, $user->passwordHash())) {
            throw new RuntimeException('Invalid credentials');
        }

        return $user;
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
            active: true,
            roles: $roles
        );

        $this->users->save($user);

        return $user;
    }
}
?>