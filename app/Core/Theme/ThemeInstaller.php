<?php

declare(strict_types=1);

namespace Keystone\Core\Theme;

use Psr\Http\Message\UploadedFileInterface;
use Keystone\Infrastructure\Paths;
use Keystone\Core\Theme\Exception\ThemeException;

use RuntimeException;
use ZipArchive;

final class ThemeInstaller implements ThemeInstallerInterface {
    
    public function __construct(
        private readonly Paths $paths
    ) {}

    public function install(UploadedFileInterface $archive): void {
        $tmp = sys_get_temp_dir() . '/theme_' . uniqid();
        mkdir($tmp);

        $zip = new ZipArchive();

        if ($zip->open($archive->getStream()->getMetadata('uri')) !== true) {
            throw new ThemeException('Invalid zip archive.');
        }

        $zip->extractTo($tmp);
        $zip->close();

        if (! file_exists($tmp . '/theme.json')) {
            throw new ThemeException('theme.json ontbreekt.');
        }

        $manifest = json_decode(file_get_contents($tmp . '/theme.json'), true);

        $this->validateManifest($manifest);

        $target = $this->paths->themes() . '/' . $manifest['name'];

        if (file_exists($target)) {
            throw new ThemeException('Theme bestaat al.');
        }

        rename($tmp, $target);
    }

    public function uninstall(string $themeName): void
    {
        $path = $this->themesPath . '/' . $themeName;

        if (! is_dir($path)) {
            throw new ThemeException('Theme niet gevonden.');
        }

        $this->deleteDirectory($path);
    }

    private function validateManifest(array $manifest): void
    {
        foreach (['name', 'version'] as $field) {
            if (! isset($manifest[$field])) {
                throw new ThemeException("theme.json mist [$field]");
            }
        }

        if (! preg_match('/^[a-z0-9\-]+$/', $manifest['name'])) {
            throw new ThemeException('Ongeldige theme naam.');
        }
    }

    private function deleteDirectory(string $dir): void
    {
        foreach (scandir($dir) as $file) {
            if ($file === '.' || $file === '..') continue;

            $path = "$dir/$file";
            is_dir($path)
                ? $this->deleteDirectory($path)
                : unlink($path);
        }

        rmdir($dir);
    }
}


?>