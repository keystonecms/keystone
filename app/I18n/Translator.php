<?php


namespace Keystone\I18n;

use Keystone\Infrastructure\Paths;

final class Translator {

    private array $messages = [];
    private ?string $loadedLocale = null;

    public function __construct(
        private LocaleContext $localeContext,
        private Paths $paths
    ) {}

public function trans(string $key, array $params = []): string {
    $this->ensureLoaded();

    $text = $this->resolve($key) ?? $key;

    foreach ($params as $name => $value) {
        $text = str_replace(
            '{' . $name . '}',
            (string) $value,
            $text
        );
    }

    return $text;
}


private function ensureLoaded(): void
{
    $locale = $this->localeContext->getLocale();

    // BELANGRIJK: gebruik strict check
    if ($this->loadedLocale === $locale && !empty($this->messages)) {
        return;
    }

    $file = $this->paths->resources() . '/' . $locale . '.php';

    if (!is_file($file)) {
        $this->messages = [];
        $this->loadedLocale = $locale;
        return;
    }

    $data = require $file;

    if (!is_array($data)) {
        throw new \RuntimeException('Translation file must return array');
    }

    $this->messages = $data;
    $this->loadedLocale = $locale;
}


    private function resolve(string $key): ?string
    {
        $segments = explode('.', $key);
        $value = $this->messages;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return null;
            }
            $value = $value[$segment];
        }

        return is_string($value) ? $value : null;
    }
}


?>