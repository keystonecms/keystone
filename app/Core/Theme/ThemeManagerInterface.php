<?php

declare(strict_types=1);

namespace Keystone\Core\Theme;

use Keystone\Core\Theme\Theme;
use Keystone\Core\Theme\ThemeManifest;

interface ThemeManagerInterface {
    /**
     * Bootstraps the theme system.
     * Called once during application boot.
     */
    public function boot(): void;

    /**
     * Returns the currently active theme.
     */
    public function getActiveTheme(): Theme;

    /**
     * Activates a theme by name.
     */
    public function activate(string $themeName): void;

    /**
     * Returns all discovered themes.
     *
     * @return Theme[]
     */
    public function all(): array;

    /**
     * Checks whether a theme exists.
     */
    public function exists(string $themeName): bool;

    /**
     * Resolves a Twig template path using theme override rules.
     */
    public function resolveTemplate(string $template): string;

    /**
     * Registers Twig namespaces for themes.
     */
    public function registerTwigNamespaces(): void;

    /**
     * Registers theme assets (CSS / JS).
     */
    public function registerAssets(): void;
}


?>