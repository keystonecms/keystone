<?php

declare(strict_types=1);

namespace Keystone\Infrastructure\Persistence;

use Keystone\Domain\Page\Page;
use Keystone\Domain\Page\PageRepositoryInterface;

final class PageRepository implements PageRepositoryInterface
{
    public function find(int $id): ?Page
    {
        return null;
    }

    public function findBySlug(string $slug): ?Page
    {
        return null;
    }

    public function all(): array
    {
        return [];
    }

    public function save(Page $page): void
    {
    }

    public function delete(Page $page): void
    {
    }
}
