<?php

namespace Keystone\Plugins\Seo\Domain;

use Keystone\Plugins\Seo\Domain\{
    SeoRepositoryInterface,
    SeoSubject,
    SeoMetadata,
};


final class SeoService {
    private const MAX_TITLE_LENGTH = 60;
    private const MAX_DESCRIPTION_LENGTH = 160;

    public function __construct(
        private SeoRepositoryInterface $repository
    ) {}

    /**
     * Use-case: ophalen van SEO metadata voor een subject
     */
    public function getForSubject(
        SeoSubject $subject,
        string $fallbackTitle = '',
        string $fallbackDescription = '',
        string $fallbackSlug,
        string $baseUrl
    ): SeoMetadata {
        $seo = $this->repository->find($subject);

        if ($seo === null) {
            return $this->createDefaultSeo(
                $fallbackTitle,
                $fallbackDescription,
                $fallbackSlug,
                $baseUrl
            );
        }

        return $this->applyDefaults(
            $seo,
            $fallbackTitle,
            $fallbackDescription,
            $fallbackSlug,
            $baseUrl
        );
    }
/**
 * generate canonical URL
 */
private function generateCanonical(
    string $baseUrl,
    string $slug
): string {
    return rtrim($baseUrl, '/') . '/' . ltrim($slug, '/');
}


    /**
     * Use-case: opslaan van SEO metadata
     */
    public function update(
        SeoSubject $subject,
        SeoMetadata $seo
    ): void {
        $normalized = $this->normalize($seo);
        $this->repository->save($subject, $normalized);
    }

    // -----------------------------
    // Business rules
    // -----------------------------

private function createDefaultSeo(
    string $fallbackTitle,
    string $fallbackDescription,
    string $fallbackSlug,
    string $baseUrl
): SeoMetadata {
    $canonical = null;

    if ($fallbackSlug !== '') {
        $canonical =
            rtrim($baseUrl, '/') . '/' . ltrim($fallbackSlug, '/');
    }

    $title = $this->trim($fallbackTitle, self::MAX_TITLE_LENGTH);
    $description = $this->trim(
        $fallbackDescription,
        self::MAX_DESCRIPTION_LENGTH
    );

    return new SeoMetadata(
        title: $title,
        description: $description,
        noIndex: false,
        canonical: $canonical,
        openGraph: [
            'title' => $title,
            'description' => $description,
            'type' => 'website',
            'url' => $canonical,
        ],
        twitter: [
            'card' => 'summary_large_image',
            'title' => $title,
            'description' => $description,
        ]
    );
}



private function applyDefaults(
    SeoMetadata $seo,
    string $fallbackTitle,
    string $fallbackDescription,
    string $fallbackSlug,
    string $baseUrl
): SeoMetadata {

    return new SeoMetadata(
        title: $seo->title() ?: $fallbackTitle,
        description: $seo->description() ?: $fallbackDescription,
        noIndex: $seo->noIndex(),
        canonical: $seo->canonical(),
        openGraph: $seo->openGraph()
    );
}


    private function normalize(SeoMetadata $seo): SeoMetadata
    {
        return new SeoMetadata(
            title: $this->trim($seo->title(), self::MAX_TITLE_LENGTH),
            description: $this->trim($seo->description(), self::MAX_DESCRIPTION_LENGTH),
            noIndex: $seo->noIndex(),
            canonical: $seo->canonical(),
            openGraph: $this->normalizeOpenGraph($seo)
        );
    }

    // -----------------------------
    // Helpers (pure domain)
    // -----------------------------

private function buildOpenGraph(
    SeoMetadata $seo,
    string $baseUrl,
    string $fallbackSlug
): array {
    return array_merge(
        [
            'title' => $seo->title(),
            'description' => $seo->description(),
            'type' => 'website',
            'url' => rtrim($baseUrl, '/') . '/' . ltrim($fallbackSlug, '/'),
        ],
        $seo->openGraph()
    );
}

private function buildTwitter(
    SeoMetadata $seo
): array {
    return array_merge(
        [
            'card' => 'summary_large_image',
            'title' => $seo->title(),
            'description' => $seo->description(),
        ],
        $seo->twitter()
    );
}


    private function normalizeOpenGraph(
        SeoMetadata $seo
    ): array {
        return array_merge(
            [
                'title' => $seo->title(),
                'description' => $seo->description(),
            ],
            $seo->openGraph()
        );
    }

    private function trim(string $value, int $maxLength): string
    {
        if (mb_strlen($value) <= $maxLength) {
            return $value;
        }

        return mb_substr($value, 0, $maxLength);
    }
}



?>