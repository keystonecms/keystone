<?php

declare(strict_types=1);

namespace Keystone\Domain\Menu\Service;

use Keystone\Domain\Menu\Entity\MenuItem;

final class LinkResolver {
    /**
     * @var array<string, callable>
     */
    private array $resolvers = [];

    public function register(string $type, callable $resolver): void
    {
        $this->resolvers[$type] = $resolver;
    }

    public function resolve(MenuItem $item): string
    {
       $type = $item->linkType();

        if (!isset($this->resolvers[$type])) {
            return '#';
        }

        return (string) ($this->resolvers[$type])($item);
    }
}


?>