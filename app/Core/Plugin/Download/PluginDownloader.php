<?php

declare(strict_types=1);

namespace Keystone\Core\Plugin\Download;

use RuntimeException;
use Keystone\Infrastructure\Paths;

final class PluginDownloader {

public function __construct(
    private Paths $paths
    ) {}



    /**
     * Download plugin ZIP from GitHub
     */
    public function download(string $slug): string {

        $url = $this->buildDownloadUrl($slug);

        $target = $this->paths->downloads() . '/' . $slug . '.zip';

        $data = @file_get_contents($url);

        if ($data === false) {
            throw new RuntimeException(
                "Unable to download plugin [$slug] from GitHub."
            );
        }

        file_put_contents($target, $data);

        return $target;
    }

    /**
     * Fetch remote plugin manifest (for update checks)
     */
    public function fetchLatestManifest(string $slug): object {
        $url = $this->buildRawManifestUrl($slug);

        $json = @file_get_contents($url);

        if ($json === false) {
            throw new RuntimeException(
                "Unable to fetch manifest for plugin [$slug]."
            );
        }

        return json_decode($json, false, 512, JSON_THROW_ON_ERROR);
    }

    private function buildDownloadUrl(string $slug): string {
        return sprintf(
            'https://github.com/keystone-cms/%s/archive/refs/heads/main.zip',
            $slug
        );
    }

    private function buildRawManifestUrl(string $slug): string {
        return sprintf(
            'https://raw.githubusercontent.com/keystone-cms/%s/main/plugin.json',
            $slug
        );
    }
}


?>