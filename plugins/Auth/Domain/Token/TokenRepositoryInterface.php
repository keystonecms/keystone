<?php

namespace Keystone\Plugins\Auth\Domain\Token;

use DateTimeImmutable;
use Keystone\Plugins\Auth\Domain\Token\Token;
use Keystone\Plugins\Auth\Domain\Token\TokenType;


interface TokenRepositoryInterface {
    public function create(
        int $userId,
        TokenType $type,
        string $hash,
        DateTimeImmutable $expiresAt
    ): void;

    public function findValid(
        string $hash,
        TokenType $type
    ): ?Token;

    public function markUsed(string $hash): void;
}


?>