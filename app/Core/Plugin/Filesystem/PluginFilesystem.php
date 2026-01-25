<?php

declare(strict_types=1);

namespace Keystone\Core\Plugin\Filesystem;

use RuntimeException;
use ZipArchive;
use Keystone\Infrastructure\Paths;
use Keystone\Core\Plugin\PluginManifest;
use Keystone\Core\Plugin\PluginInterface;

final class PluginFilesystem {
    
    public function __construct(
        private Paths $paths
    ) {
        $this->pluginDir = $this->paths->plugins();
        $this->varDir    = $this->paths->pluginsbackup();
    }
    
    /**
     * Extract a downloaded ZIP into plugins/{PluginName}
     */
public function loadPluginClass(PluginManifest $manifest): PluginInterface {
    $pluginName = ucfirst($manifest->slug);

    $pluginPath = $this->pluginDir . '/' . $pluginName . '/src/Plugin.php';

    if (!file_exists($pluginPath)) {
        throw new RuntimeException(
            "Plugin entry file not found: {$pluginPath}"
        );
    }

    require_once $pluginPath;

    $class = "Keystone\\Plugin\\{$pluginName}\\Plugin";

    if (!class_exists($class)) {
        throw new RuntimeException(
            "Plugin entry class {$class} not found."
        );
    }

    $plugin = new $class();

    if (!$plugin instanceof PluginInterface) {
        throw new RuntimeException(
            "Plugin {$class} must implement PluginInterface."
        );
    }

    return $plugin;
}


    /**
     * Extract a downloaded ZIP into plugins/{PluginName}
     */
    public function extract(string $archive, string $slug): string {
        $pluginName = ucfirst($slug);
        $targetDir  = "{$this->pluginDir}/{$pluginName}";

        if (is_dir($targetDir)) {
            $this->deleteDir($targetDir);
        }

        $zip = new ZipArchive();

        if ($zip->open($archive) !== true) {
            throw new RuntimeException("Unable to open plugin archive.");
        }

        $zip->extractTo($this->pluginDir);
        $zip->close();

        // GitHub ZIP contains repo-name-main/
        $extracted = glob($this->pluginDir . '/*', GLOB_ONLYDIR);
        $repoDir   = end($extracted);

        rename($repoDir, $targetDir);

        return $targetDir;
    }

    /**
     * Backup current plugin before update
     */
    public function backup(string $slug): void
    {
        $pluginName = ucfirst($slug);
        $source     = "{$this->pluginDir}/{$pluginName}";

        if (!is_dir($source)) {
            return;
        }

        $backupDir = "{$this->varDir}/backups/{$pluginName}_" . date('YmdHis');

        $this->copyDir($source, $backupDir);
    }

    /**
     * Restore last backup (optional use)
     */
    public function restoreBackup(string $slug): void
    {
        $pluginName = ucfirst($slug);
        $backupRoot = "{$this->varDir}/backups";

        $backups = glob("{$backupRoot}/{$pluginName}_*", GLOB_ONLYDIR);

        if (!$backups) {
            return;
        }

        rsort($backups);
        $latest = $backups[0];

        $target = "{$this->pluginDir}/{$pluginName}";

        $this->deleteDir($target);
        $this->copyDir($latest, $target);
    }

    /**
     * Read plugin.json
     */
public function readManifest(string $pluginPath): PluginManifest {

    $file = $pluginPath . '/plugin.json';

    if (!file_exists($file)) {
        throw new RuntimeException('plugin.json not found.');
    }

    try {
        $data = json_decode(
            file_get_contents($file),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    } catch (\JsonException $e) {
        throw new RuntimeException(
            'Invalid plugin.json: ' . $e->getMessage()
        );
    }


if (empty($data['slug']) || empty($data['version'])) {
    throw new RuntimeException('Invalid plugin.json: missing required fields.');
}


    return new PluginManifest(
        slug: $data['slug'],
        name: $data['name'],
        package: $data['package'],
        version: $data['version'],
        keystone: $data['keystone'] ?? '*',
        migrations: (bool) ($data['migrations'] ?? false),
        assets: (bool) ($data['assets'] ?? false),
    );
}


    /**
     * Run plugin migrations
     */
    public function runMigrations(string $pluginPath): void
    {
        $dir = $pluginPath . '/migrations';

        if (!is_dir($dir)) {
            return;
        }

        foreach (glob($dir . '/*.php') as $migration) {
            require $migration;
        }
    }

    /**
     * Publish plugin assets
     */
    public function publishAssets(string $pluginPath, string $slug): void
    {
        $source = $pluginPath . '/assets';

        if (!is_dir($source)) {
            return;
        }

        $target = "public/assets/plugins/{$slug}";

        $this->deleteDir($target);
        $this->copyDir($source, $target);
    }

    /* -------------------- helpers -------------------- */

private function deleteDir(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }

    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator(
            $dir,
            \FilesystemIterator::SKIP_DOTS
        ),
        \RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $file) {
        $path = $file->getPathname();

        if ($file->isDir()) {
            rmdir($path);
        } else {
            unlink($path);
        }
    }

    rmdir($dir);
}


    private function copyDir(string $src, string $dst): void
    {
        mkdir($dst, 0755, true);

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $src,
                \FilesystemIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            $target = $dst . '/' . $files->getSubPathName();

            if ($file->isDir()) {
                mkdir($target, 0755, true);
            } else {
                copy($file, $target);
            }
        }
    }
}


?>