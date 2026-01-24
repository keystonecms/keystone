<?php

declare(strict_types=1);

namespace Keystone\Domain\Role;

interface RoleRepositoryInterface {

public function find($roleId): array;

public function all(): array;

public function findOrCreate(string $name, string $label): int;

public function syncPolicies(int $roleId, array $policyIds): void;

public function policyIds(int $roleId): array;

public function create(array $data): int;


}

?>
