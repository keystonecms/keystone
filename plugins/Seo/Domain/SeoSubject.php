<?php

namespace Keystone\Plugins\Seo\Domain;

final class SeoSubject {
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