<?php

declare(strict_types=1);

namespace Keystone\Plugins\Seo\Twig;

use Keystone\Plugins\Seo\Domain\SeoMetadata;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class SeoExtension extends AbstractExtension {
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'render_seo',
                [$this, 'render'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function render(SeoMetadata $seo): string
    {
        $html = [];

        // <title>
        if ($seo->title() !== '') {
            $html[] = '<title>' . htmlspecialchars($seo->title(), ENT_QUOTES) . '</title>';
        }

        // meta description
        if ($seo->description() !== '') {
            $html[] = sprintf(
                '<meta name="description" content="%s">',
                htmlspecialchars($seo->description(), ENT_QUOTES)
            );
        }

        // canonical
        if ($seo->canonical() !== null) {
            $html[] = sprintf(
                '<link rel="canonical" href="%s">',
                htmlspecialchars($seo->canonical(), ENT_QUOTES)
            );
        }

        // robots
        if ($seo->noIndex()) {
            $html[] = '<meta name="robots" content="noindex, nofollow">';
        }

        // Open Graph
        foreach ($seo->openGraph() as $property => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $html[] = sprintf(
                '<meta property="og:%s" content="%s">',
                htmlspecialchars($property, ENT_QUOTES),
                htmlspecialchars((string) $value, ENT_QUOTES)
            );
        }

        // Twitter Cards
        foreach ($seo->twitter() as $name => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $html[] = sprintf(
                '<meta name="twitter:%s" content="%s">',
                htmlspecialchars($name, ENT_QUOTES),
                htmlspecialchars((string) $value, ENT_QUOTES)
            );
        }



        return implode("\n", $html);
    }
}
