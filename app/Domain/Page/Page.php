<?php

namespace Keystone\Domain\Page;

final class Page {
    public function __construct(
        private int $id,
        private string $slug,
        private array $blocks // raw JSON array
    ) {}

    public function blocks(): array
    {
        return $this->blocks;
    }

    public function updateBlocks(array $blocks): void
    {
        $this->blocks = $blocks;
    }
}


?>