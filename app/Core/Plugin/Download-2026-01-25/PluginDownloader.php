<?php

declare(strict_types=1);

namespace Keystone\Core\Plugin\Download;

use RuntimeException;
use Keystone\Infrastructure\Paths;
use Psr\Log\LoggerInterface;

final class PluginDownloader {

public function __construct(
    private Paths $paths,
    private LoggerInterface $logger
    ) {}



    /**
     * Download plugin ZIP from GitHub
     */
    public function download(string $slug): string {

        $url = $this->buildDownloadUrl($slug);
        
        if (!is_dir($this->paths->downloads())) {
            mkdir($this->paths->downloads());
        }

        $target = $this->paths->downloads() . '/' . $slug . '.zip';
        
        $data = @file_get_contents($url);

        if ($data === false) {

            $error = error_get_last();

            $this->logger->error(
                'Plugin download failed',
                [
                    'slug' => $slug,
                    'url'  => $url,
                    'php_error' => $error['message'] ?? 'unknown',
                ]
            );

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
            'https://github.com/keystonecms/plugin-%s/archive/refs/heads/master.zip',
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