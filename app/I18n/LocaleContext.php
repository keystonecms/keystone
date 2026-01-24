<?php

namespace Keystone\I18n;

final class LocaleContext {

    private string $locale;
    private string $defaultLocale;

    public function __construct(string $defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
        $this->locale = $defaultLocale;
    }

    public function getLanguage() {
        return substr($this->locale,0,2);
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }
}



?>