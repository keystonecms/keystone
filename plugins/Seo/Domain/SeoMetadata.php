<?php

namespace Keystone\Plugins\Seo\Domain;

final class SeoMetadata {
    public function __construct(
        private string $title,
        private string $description,
        private bool $noIndex = false,
        private ?string $canonical = null,
        private array $openGraph = [],
        private array $twitter = []
    ) {}

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function noIndex(): bool
    {
        return $this->noIndex;
    }

    public function canonical(): ?string
    {
        return $this->canonical;
    }

    public function openGraph(): array
    {
        return $this->openGraph;
    }

        public function twitter(): array
    {
        return $this->twitter;
    }
}
