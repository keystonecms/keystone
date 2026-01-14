<?php

namespace Keystone\Domain\User;

final class UserPolicy
{
    public function manage(User $actor): bool
    {
        return $actor->hasRole('admin');
    }

    public function view(User $actor, User $subject): bool
    {
        return
            $actor->hasRole('admin')
            || $actor->id() === $subject->id();
    }
}
