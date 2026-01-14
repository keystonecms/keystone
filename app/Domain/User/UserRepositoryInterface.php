<?php

namespace Keystone\Domain\User;

interface UserRepositoryInterface {
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function save(User $user): void;
}
