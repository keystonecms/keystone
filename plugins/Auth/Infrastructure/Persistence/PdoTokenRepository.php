<?php

declare(strict_types=1);

namespace Keystone\Plugins\Auth\Infrastructure\Persistence;

use DateTimeImmutable;
use PDO;
use Keystone\Plugins\Auth\Domain\Token\Token;
use Keystone\Plugins\Auth\Domain\Token\TokenType;
use Keystone\Plugins\Auth\Domain\Token\TokenRepositoryInterface;

final class PdoTokenRepository implements TokenRepositoryInterface {
    public function __construct(
        private PDO $pdo
    ) {}

    public function create(
        int $userId,
        TokenType $type,
        string $hash,
        DateTimeImmutable $expiresAt
    ): void {

        $stmt = $this->pdo->prepare(
            'INSERT INTO user_tokens (user_id, type, token_hash, expires_at)
             VALUES (:user_id, :type, :hash, :expires_at)'
        );

        $stmt->execute([
            'user_id'    => $userId,
            'type'       => $type->value,
            'hash'       => $hash,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);
    }

    public function findValid(
        string $hash,
        TokenType $type
    ): ?Token {
        $stmt = $this->pdo->prepare(
            'SELECT user_id, expires_at
             FROM user_tokens
             WHERE token_hash = :hash
               AND type = :type
               AND used_at IS NULL
               AND expires_at > NOW()
             LIMIT 1'
        );

        $stmt->execute([
            'hash' => $hash,
            'type' => $type->value,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (! $row) {
            return null;
        }

        return new Token(
            userId: (int) $row['user_id'],
            type: $type,
            hash: $hash,
            expiresAt: new DateTimeImmutable($row['expires_at'])
        );
    }

    public function markUsed(string $hash): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE user_tokens
             SET used_at = NOW()
             WHERE token_hash = :hash'
        );

        $stmt->execute(['hash' => $hash]);
    }
}


?>