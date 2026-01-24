<?php

namespace Keystone\Core\Activity;

use PDO;
use Keystone\Infrastructure\Activity\ActivityRepository;

final class ActivityService {
    public function __construct(
        private PDO $pdo,
        private ActivityRepository $activeRepository
    ) {}

    public function log(
        string $message,
        ?string $actorType = null,
        ?int $actorId = null,
        ?string $context = null,
        ?int $contextId = null,
    ): void {
        
      $this->activeRepository->log($message, $actorType, $actorId, $context, $contextId);

    }
}

?>