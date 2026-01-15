<?php

declare(strict_types=1);

namespace Keystone\Plugins\InternalLinks\Domain;

final class InternalLink
{
    public function __construct(
        private LinkSubject $from,
        private LinkSubject $to,
        private string $anchorText,
        private bool $nofollow = false
    ) {}

    public function from(): LinkSubject
    {
        return $this->from;
    }

    public function to(): LinkSubject
    {
        return $this->to;
    }

    public function anchorText(): string
    {
        return $this->anchorText;
    }

    public function nofollow(): bool
    {
        return $this->nofollow;
    }
}

?>