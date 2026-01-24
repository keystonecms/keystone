<?php

declare(strict_types=1);

namespace Keystone\Domain\Menu\Entity;

final class Menu {
    /**
     * @param MenuItem[] $items
     */
    public function __construct(
        private int $id,
        private string $name,
        private string $handle,
        private ?string $description,
        private array $items = []
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function handle(): string
    {
        return $this->handle;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    /**
     * @return MenuItem[]
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @param MenuItem[] $items
     */
    public function withItems(array $items): self
    {
        return new self(
            $this->id,
            $this->name,
            $this->handle,
            $this->description,
            $items
        );
    }
}

?>