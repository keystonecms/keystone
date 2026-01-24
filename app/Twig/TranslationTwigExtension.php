<?php

namespace Keystone\Twig;

use Keystone\I18n\Translator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class TranslationTwigExtension extends AbstractExtension {

public function __construct(
        private Translator $translator
    ) {}

    public function getFilters(): array
    {
        return [
            new TwigFilter('trans', [$this->translator, 'trans']),
        ];
    }
}

?>