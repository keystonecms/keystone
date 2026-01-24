<?php

namespace Keystone\Infrastructure\Persistence;

use PDO;
use Keystone\Domain\Policy\PolicyRepositoryInterface;

final class PolicyRepository implements PolicyRepositoryInterface {

public function __construct(
    private PDO $pdo,
) {}


public function findOrCreate(
    string $key,
    string $label,
    string $category
): int {
    $stmt = $this->pdo->prepare(
        'SELECT id FROM policies WHERE key_name = :key'
    );
    $stmt->execute(['key' => $key]);

    $id = $stmt->fetchColumn();
    if ($id) {
        return (int) $id;
    }

    $insert = $this->pdo->prepare(
        'INSERT INTO policies (key_name, label, category)
         VALUES (:key, :label, :category)'
    );

    $insert->execute([
        'key'      => $key,
        'label'    => $label,
        'category' => $category,
    ]);

    return (int) $this->pdo->lastInsertId();
}

public function findAll(): array
{
    $stmt = $this->pdo->query(
        'SELECT id, key_name, label, category
         FROM policies
         ORDER BY key_name'
    );

    return $stmt->fetchAll();
}


public function idsByKeys(array $keys): array {
    if (empty($keys)) {
        return [];
    }

    $in = implode(',', array_fill(0, count($keys), '?'));

    $stmt = $this->pdo->prepare(
        "SELECT id FROM policies WHERE key_name IN ($in)"
    );

    $stmt->execute($keys);

    return array_map(
        'intval',
        $stmt->fetchAll(\PDO::FETCH_COLUMN)
    );
}


public function allIds(): array {
    return array_map(
        'intval',
        $this->pdo
            ->query('SELECT id FROM policies')
            ->fetchAll(\PDO::FETCH_COLUMN)
    );
  }
}

?>