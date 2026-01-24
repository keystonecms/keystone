<?php

namespace Keystone\Infrastructure\Activity;

use PDO;

final class ActivityRepository {
    public function __construct(
        private PDO $pdo,
    ) {}

    public function log(
        string $message,
        ?string $actorType = null,
        ?int $actorId = null,
        ?string $context = null,
        ?int $contextId = null,
    ): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO activity_log
                (message, actor_type, actor_id, context, context_id, occurred_at)
             VALUES
                (:message, :actor_type, :actor_id, :context, :context_id, NOW())'
        );

        $stmt->execute([
            'message'    => $message,
            'actor_type' => $actorType,
            'actor_id'   => $actorId,
            'context'    => $context,
            'context_id' => $contextId,
        ]);
    }
}
