<?php

namespace Keystone\Core\System;

interface ErrorRepositoryInterface {
    public function create(array $data): void;

    public function findUnresolved(int $limit = 50): array;

    public function find(int $id): array;

    public function markResolved(int $id, int $userId): void;
}

?>
