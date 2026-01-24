<?php

namespace Keystone\Infrastructure\Persistence;

use PDO;
use Keystone\Domain\Role\RoleRepositoryInterface;

final class RoleRepository implements RoleRepositoryInterface {

    public function __construct(
        private PDO $pdo,
    ) {}


public function findOrCreate(string $name, string $label): int {
    $stmt = $this->pdo->prepare(
        'SELECT id FROM roles WHERE name = :name'
    );
    $stmt->execute(['name' => $name]);

    $id = $stmt->fetchColumn();

    if ($id) {
        return (int) $id;
    }

    $insert = $this->pdo->prepare(
        'INSERT INTO roles (name, label)
         VALUES (:name, :label)'
    );

    $insert->execute([
        'name'  => $name,
        'label' => $label,
    ]);

    return (int) $this->pdo->lastInsertId();
}

public function findAllWithStats(): array {
    $stmt = $this->pdo->query(
        'SELECT
            r.id,
            r.name,
            r.label,
            COUNT(DISTINCT ur.user_id)   AS user_count,
            COUNT(DISTINCT rp.policy_id) AS policy_count
         FROM roles r
         LEFT JOIN user_roles ur ON ur.role_id = r.id
         LEFT JOIN role_policies rp ON rp.role_id = r.id
         GROUP BY r.id
         ORDER BY r.name'
    );

    return $stmt->fetchAll();
}

public function syncPolicies(int $roleId, array $policyIds): void {
        $this->pdo->beginTransaction();

        // 1. verwijder bestaande koppelingen
        $delete = $this->pdo->prepare(
            'DELETE FROM role_policies WHERE role_id = :role'
        );
        $delete->execute([
            'role' => $roleId,
        ]);

        // 2. voeg nieuwe koppelingen toe
        if (!empty($policyIds)) {
            $insert = $this->pdo->prepare(
                'INSERT INTO role_policies (role_id, policy_id)
                 VALUES (:role, :policy)'
            );

            foreach ($policyIds as $policyId) {
                $insert->execute([
                    'role'   => $roleId,
                    'policy' => (int) $policyId,
                ]);
            }
        }

        $this->pdo->commit();
}

public function create(array $data): int {
    $stmt = $this->pdo->prepare(
        'INSERT INTO roles (name, label)
         VALUES (:name, :name)'
    );

    $stmt->execute([
        'name' => $data['name'],
    ]);

    return (int) $this->pdo->lastInsertId();
}


public function policyIds(int $roleId): array
{
    $stmt = $this->pdo->prepare(
        'SELECT policy_id
         FROM role_policies
         WHERE role_id = :role'
    );

    $stmt->execute(['role' => $roleId]);

    return array_column(
        $stmt->fetchAll(),
        'policy_id'
    );
}


public function find($roleId): array {

        $stmt = $this->pdo->prepare(
            'SELECT id, name, label
               FROM roles
               WHERE id = :role
               ORDER BY name LIMIT 1'
        );
  
         $stmt->execute(['role' => (int) $roleId]);

        return  $stmt->fetch(PDO::FETCH_ASSOC);
    }

public function all(): array {
        $stmt = $this->pdo->query(
            'SELECT id, name, label
               FROM roles
               ORDER BY name'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


?>