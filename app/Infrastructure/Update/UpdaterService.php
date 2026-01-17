<?php

declare(strict_types=1);

namespace Keystone\Infrastructure\Update;

use RuntimeException;
use ZipArchive;

final class UpdaterService {

public function activate(
    string $zipPath,
    string $version,
    string $projectRoot
): void {
    // 1. Dry-run eerst
    $result = $this->dryRun($zipPath);

    if (!$result->isOk()) {
        throw new RuntimeException('Preflight failed');
    }

    // 2. Extract opnieuw (schone temp)
    $tmpDir = sys_get_temp_dir() . '/keystone_release_' . uniqid();
    mkdir($tmpDir);

    $zip = new ZipArchive();
    $zip->open($zipPath);
    $zip->extractTo($tmpDir);
    $zip->close();

    // 3. Activate
    (new ReleaseActivator())->activate(
        $version,
        $tmpDir,
        $projectRoot
    );
}


    public function dryRun(string $zipPath): PreflightResult
    {
        $result = new PreflightResult();

        // 1. PHP version
        $result->add(
            'php_version',
            version_compare(PHP_VERSION, '8.2', '>='),
            'PHP version check'
        );

        // 2. Extract ZIP to temp dir
        $tmpDir = sys_get_temp_dir() . '/keystone_update_' . uniqid();
        mkdir($tmpDir);

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new RuntimeException('Unable to open ZIP');
        }
        $zip->extractTo($tmpDir);
        $zip->close();

        // 3. Manifest
        $manifestFile = $tmpDir . '/manifest.json';
        if (!file_exists($manifestFile)) {
            $result->add('manifest', false, 'manifest.json missing');
            return $result;
        }

        $manifestData = json_decode(
            file_get_contents($manifestFile),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $manifest = ReleaseManifest::fromArray($manifestData);

        // 4. Required files
        foreach ($manifest->requiredFiles as $file) {
            $ok = file_exists($tmpDir . '/' . $file);
            $result->add("file:$file", $ok, $file);
        }

        // 5. Required directories
        foreach ($manifest->requiredDirectories as $dir) {
            $ok = is_dir($tmpDir . '/' . $dir);
            $result->add("dir:$dir", $ok, $dir);
        }

        return $result;
    }
}

?>