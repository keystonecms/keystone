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
        private int $authorId,
        private string $template,
        private bool $isHomepage,
        private int $published_version_id,
        private ?string $next_publication
    ) {}

    public function id(): int { return $this->id; }
    public function title(): string { return $this->title; }
    public function slug(): string { return $this->slug; }
    public function content(): string { return $this->content; }
    public function status(): string { return $this->status; }
    public function authorId(): int { return $this->authorId; }
    public function template(): string { return $this->template; }
    public function published_version_id(): int { return $this->published_version_id; }
    public function next_publication(): ?string { return $this->next_publication; }

    public function isPublished(): bool {
        return $this->status === 'published';
    }
    public function isHomepage(): bool {
        return $this->isHomepage;
    }

    }

    ?>