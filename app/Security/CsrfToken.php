<?php

declare(strict_types=1);

namespace Keystone\Security;

final class CsrfToken
{
    private const SESSION_KEY = '_csrf_token';

    public function generate(): string
    {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public function validate(?string $token): bool
    {
        if (!$token || !isset($_SESSION[self::SESSION_KEY])) {
            return false;
        }

        return hash_equals($_SESSION[self::SESSION_KEY], $token);
    }
}

?>