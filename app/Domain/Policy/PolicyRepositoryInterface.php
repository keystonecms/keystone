<?php

declare(strict_types=1);

namespace Keystone\Domain\Policy;

interface PolicyRepositoryInterface {

public function allIds(): array;

public function idsByKeys(array $keys): array;

public function findAll(): array;

public function findOrCreate(
    string $key,
    string $label,
    string $category
): int; 

}

?>