<?php

declare(strict_types=1);

/*
 * Keystone CMS
 *
 * @package   Keystone CMS
 * @license   MIT
 * @link      https://keystone-cms.com
 */

namespace Keystone\Plugins\Pages\Domain;

final class Page
{
    public function __construct(
        private int $id,
        private string $title,
        private string $slug,
        private string $content,
        private string $status,
        private int $authorId
    ) {}

    public function id(): int { return $this->id; }
    public function title(): string { return $this->title; }
    public function slug(): string { return $this->slug; }
    public function content(): string { return $this->content; }
    public function status(): string { return $this->status; }
    public function authorId(): int { return $this->authorId; }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
