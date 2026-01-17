<?php

declare(strict_types=1);

namespace Keystone\Plugins\Auth\Domain\Token;

use DateTimeImmutable;

final class Token {
    public function __construct(
        private int $userId,
        private TokenType $type,
        private string $hash,
        private DateTimeImmutable $expiresAt
    ) {}

    public function userId(): int
    {
        return $this->userId;
    }

    public function type(): TokenType
    {
        return $this->type;
    }

    public function hash(): string
    {
        return $this->hash;
    }

    public function expiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt <= new DateTimeImmutable();
    }
}

?>