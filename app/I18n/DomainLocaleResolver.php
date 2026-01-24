<?php
namespace Keystone\I18n;

final class DomainLocaleResolver {
    /** @var array<string,string> */
    private array $map;

    public function __construct(array $map)
    {

    foreach ($map as $domain => $locale) {
        if (!is_string($domain) || !is_string($locale)) {
            throw new \InvalidArgumentException(
                'DomainLocaleResolver expects string => string map'
            );
        }
    }

        $this->map = $map;
    }

    public function resolve(string $host): ?string
    {
        return $this->map[$host] ?? null;
    }
}

?>