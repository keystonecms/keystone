<?php

namespace Keystone\Twig;

use Keystone\I18n\LocaleContext;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class LocaleTwigExtension extends AbstractExtension implements GlobalsInterface {

    public function __construct(
        private LocaleContext $localeContext
    ) {}

    public function getGlobals(): array
    {
        return [
            'locale' => $this->localeContext->getLocale(),
        ];
    }
}


?>