<?php

declare(strict_types=1);

namespace Keystone\Plugins\Auth\Domain\Token;

use DateTimeImmutable;

final class TokenService {
    private const EXPIRY_MINUTES = [
        TokenType::ACTIVATION->value     => 60 * 24, // 24 uur
        TokenType::PASSWORD_RESET->value => 60,      // 1 uur
    ];

    public function __construct(
        private TokenRepositoryInterface $tokens
    ) {}

    /**
     * Maakt een token en retourneert de RAW token (voor e-mail)
     */
    public function create(int $userId, TokenType $type): string
    {
        $rawToken = bin2hex(random_bytes(32)); // 64 chars
        $hash     = hash('sha256', $rawToken);

        $expiresAt = $this->expiresAt($type);

        $this->tokens->create(
            $userId,
            $type,
            $hash,
            $expiresAt
        );
 return $rawToken;
   }

    /**
     * Valideert en consumeert een token (one-time use)
     */
    public function consume(string $rawToken, TokenType $type): Token
    {
        $hash = hash('sha256', $rawToken);

        $token = $this->tokens->findValid($hash, $type);

        if ($token === null) {
            throw new InvalidTokenException();
        }

        $this->tokens->markUsed($hash);

        return $token;
    }

    private function expiresAt(TokenType $type): DateTimeImmutable
    {
        $minutes = self::EXPIRY_MINUTES[$type->value] ?? 60;

    return (new DateTimeImmutable('now', new \DateTimeZone('UTC')))
        ->modify(sprintf('+%d minutes', $minutes));
    }
}


?>