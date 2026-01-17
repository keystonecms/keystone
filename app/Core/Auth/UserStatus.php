<?php

declare(strict_types=1);

namespace Keystone\Core\Auth;

enum UserStatus: string {
    case ACTIVE   = 'active';
    case INACTIVE = 'inactive';
    case BLOCKED  = 'blocked';
    case PENDING  = 'pending';

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function canLogin(): bool
    {
        return $this === self::ACTIVE;
    }

    public function canBeAssigned(): bool
    {
        return $this === self::ACTIVE;
    }
}


?>