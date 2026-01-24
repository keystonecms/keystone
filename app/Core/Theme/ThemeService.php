<?php

declare(strict_types=1);

namespace Keystone\Core\Theme;

use Keystone\Core\Theme\ThemeManagerInterface;
use Keystone\Core\Theme\ThemeInstallerInterface;
use Psr\Http\Message\UploadedFileInterface;


final class ThemeService {
    public function __construct(
        private ThemeManagerInterface $themes,
        private ThemeInstallerInterface $installer
    ) {}

public function install(UploadedFileInterface $file): void {
        $this->installer->install($file);
    }

public function uninstall(string $name): void {
        if ($this->themes->getActiveTheme()->name === $name) {
            throw new \RuntimeException('Actieve theme kan niet verwijderd worden.');
        }

        $this->installer->uninstall($name);
    }

public function listThemes(): array {
        return $this->themes->all();
    }

public function activateTheme(string $name): void {
        $this->themes->activate($name);
    }

public function active(): string {
        return $this->themes->getActiveTheme()->name;
    }
}

?>