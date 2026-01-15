<?php

namespace Keystone\Plugins\Seo\Domain;


use Keystone\Plugins\Seo\Domain\{
    SeoSubject,
    SeoMetadata,
};

interface SeoRepositoryInterface {
    public function find(SeoSubject $subject): ?SeoMetadata;

    public function save(
        SeoSubject $subject,
        SeoMetadata $metadata
    ): void;
}
