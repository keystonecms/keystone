<?php

declare(strict_types=1);

namespace Keystone\Domain\Menu\Entity;

final class MenuItem {
    public function __construct(
        private int $id,
        private int $menuId,
        private ?int $parentId,
        private string $label,
        private string $linkType,
        private string $linkTarget,
        private int $sortOrder,
        private bool $isVisible,
        private ?string $cssClass = null,
        private ?string $target = null,
        private array $children = []
    ) {}

    public function id(): int
    {
        return $this->id;
    }
    
    public function cssClass(): ?string
{
    return $this->cssClass;
}

public function target(): ?string
{
    return $this->target;
}


    public function menuId(): int
    {
        return $this->menuId;
    }

    public function parentId(): ?int
    {
        return $this->parentId;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function linkType(): string
    {
        return $this->linkType;
    }

    public function linkTarget(): string
    {
        return $this->linkTarget;
    }

    public function sortOrder(): int
    {
        return $this->sortOrder;
    }

    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    /**
     * @return MenuItem[]
     */
    public function children(): array
    {
        return $this->children;
    }

    /**
     * @param MenuItem[] $children
     */
public function withChildren(array $children): self
{
    return new self(
        id: $this->id,
        menuId: $this->menuId,
        parentId: $this->parentId,
        label: $this->label,
        linkType: $this->linkType,
        linkTarget: $this->linkTarget,
        sortOrder: $this->sortOrder,
        isVisible: $this->isVisible,
        cssClass: $this->cssClass,
        target: $this->target,
        children: $children
        );
    }
}

?>