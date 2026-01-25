<?php

namespace Keystone\Infrastructure\System;

use Keystone\Core\System\ErrorRepositoryInterface;
use PDO;

final class ErrorRepository implements ErrorRepositoryInterface {
    public function __construct(
        private PDO $pdo
    ) {}


public function stats(): array
{
    $stmt = $this->pdo->query(
        'SELECT
            SUM(resolved = 0) AS open,
            SUM(resolved = 1) AS resolved,
            COUNT(*) AS total
         FROM system_errors'
    );

    return $stmt->fetch();
}


    public function create(array $data): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO system_errors (
                level, errorid, message, exception_class,
                file, line, trace,
                request_uri, method, user_id, plugin,
                resolved, created_at
            ) VALUES (
                :level, :errorid, :message, :exception_class,
                :file, :line, :trace,
                :request_uri, :method, :user_id, :plugin,
                0, NOW()
            )'
        );

        $stmt->execute([
            'level'           => $data['level'] ?? 'error',
            'errorid'           => $data['errorid'] ?? null,
            'message'         => $data['message'],
            'exception_class' => $data['exception_class'] ?? null,
            'file'            => $data['file'] ?? null,
            'line'            => $data['line'] ?? null,
            'trace'           => $data['trace'] ?? null,
            'request_uri'     => $data['request_uri'] ?? null,
            'method'          => $data['method'] ?? null,
            'user_id'         => $data['user_id'] ?? null,
            'plugin'          => $data['plugin'] ?? null,
        ]);
    }

    public function findUnresolved(int $limit = 50): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM system_errors
             WHERE resolved = 0
             ORDER BY created_at DESC
             LIMIT :limit'
        );

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function find(int $id): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM system_errors WHERE id = :id'
        );

        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    public function markResolved(int $id, int $userId): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE system_errors
             SET resolved = 1,
                 resolved_at = NOW(),
                 resolved_by = :user
             WHERE id = :id'
        );

        $stmt->execute([
            'id'   => $id,
            'user' => $userId,
        ]);
    }
}

?>