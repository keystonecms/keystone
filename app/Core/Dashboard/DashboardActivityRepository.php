<?php

namespace Keystone\Core\Dashboard;

use PDO;
use DateTimeImmutable;

final class DashboardActivityRepository {
    
    public function __construct(
        private PDO $pdo,
    ) {}

    /**
     * @return DashboardActivity[]
     */
    public function latest(int $limit): array {
        $stmt = $this->pdo->prepare(
            'SELECT message, occurred_at FROM activity_log ORDER BY occurred_at DESC LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(
            fn ($row) => new DashboardActivity(
                $row['message'],
                new DateTimeImmutable($row['occurred_at']),
            ),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}

?>