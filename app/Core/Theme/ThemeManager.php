<?php

declare(strict_types=1);

namespace Keystone\Core\Theme;

use Twig\Environment as Twig;
use Keystone\Core\Settings\DatabaseSettings;
use Keystone\Infrastructure\Paths;

final class ThemeManager implements ThemeManagerInterface {

private array $themes = [];

private ?Theme $activeTheme = null;

public function __construct(
        private readonly Paths $paths,
        private readonly DatabaseSettings $settings,
        private readonly Twig $twig
    ) {}

public function boot(): void {
        $this->discoverThemes();
        $this->loadActiveTheme();
        $this->registerTwigNamespaces();
    }

    public function all(): array {
        return $this->themes;
    }

    public function exists(string $themeName): bool {
        return isset($this->themes[$themeName]);
    }

public function getActiveTheme(): Theme
{
    if ($this->activeTheme === null) {
        throw new \LogicException(
            'ThemeManager not booted. Call ThemeManager::boot() during application bootstrap.'
        );
    }

    return $this->activeTheme;
}


public function resolvePageTemplate(string $layout): string
{
    $allowed = [
        'default',
        'homepage',
        'full-width',
        'landing',
    ];

    if (! in_array($layout, $allowed, true)) {
        $layout = 'default';
    }

    return $this->resolveTemplate("{$layout}.twig");
}


    public function activate(string $themeName): void
    {
        if (! $this->exists($themeName)) {
            throw new \RuntimeException("Theme [$themeName] does not exist.");
        }

        $this->settings->set('theme.active', $themeName);
        $this->activeTheme = $this->themes[$themeName];
    }

    public function resolveTemplate(string $template): string
    {
        foreach (['Theme', 'ParentTheme', 'Plugin', 'Core'] as $ns) {
            $path = "@$ns/$template";
            if ($this->twig->getLoader()->exists($path)) {
                return $path;
            }
        }

        throw new \RuntimeException("Template [$template] could not be resolved.");
    }

    public function registerTwigNamespaces(): void
    {
        $theme = $this->activeTheme;

        $this->twig->getLoader()->addPath(
            $theme->path . '/templates',
            'Theme'
        );

        if ($theme->manifest->extends) {
            $parent = $this->themes[$theme->manifest->extends] ?? null;

            if ($parent) {
                $this->twig->getLoader()->addPath(
                    $parent->path . '/templates',
                    'ParentTheme'
                );
            }
        }
    }

    public function registerAssets(): void
    {
        // bewust leeg: ThemeAssetRegistry pakt dit op
    }

    private function discoverThemes(): void
    {
        foreach (glob($this->paths->themes() . '/*/theme.json') as $file) {
            $path = dirname($file);
            $data = json_decode(file_get_contents($file), true);

            $manifest = new ThemeManifest(
                $data['name'],
                $data['version'],
                $data['extends'] ?? null
            );

            $theme = new Theme(
                $data['name'],
                $path,
                $manifest
            );

            $this->themes[$theme->name] = $theme;
        }
    }

    private function loadActiveTheme(): void
    {
        $active = $this->settings->get('theme.active', 'default');

        if (! $this->exists($active)) {
            throw new \RuntimeException("Active theme [$active] not found.");
        }

        $this->activeTheme = $this->themes[$active];
    }
}

?>