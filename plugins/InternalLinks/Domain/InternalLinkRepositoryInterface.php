<?php

declare(strict_types=1);

namespace Keystone\Plugins\InternalLinks\Domain;

interface InternalLinkRepositoryInterface
{
    /**
     * @return InternalLink[]
     */
    public function findFrom(LinkSubject $from): array;

    public function save(InternalLink $link): void;

    public function delete(InternalLink $link): void;
}


?>