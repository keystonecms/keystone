<?php

declare(strict_types=1);

namespace Keystone\Domain\Menu\Repository;

use Keystone\Domain\Menu\Entity\Menu;

interface MenuRepositoryInterface
{
    public function getByHandle(string $handle): ?Menu;

    public function getById(int $id): ?Menu;

    /**
     * @return Menu[]
     */
    public function getAll(): array;
}

?>