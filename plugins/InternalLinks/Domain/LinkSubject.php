<?php

declare(strict_types=1);

namespace Keystone\Plugins\InternalLinks\Domain;

final class LinkSubject {
    public function __construct(
        private string $type,
        private int $id
    ) {}

    public function type(): string
    {
        return $this->type;
    }

    public function id(): int
    {
        return $this->id;
    }
}


?>