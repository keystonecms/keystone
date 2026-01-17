<?php

declare(strict_types=1);

namespace Keystone\Plugins\System\Domain;

use Keystone\Plugins\System\Infrastructure\GitHub\GitHubReleaseFetcher;
use Psr\Log\LoggerInterface;

final class UpdateStatusService {
    public function __construct(
        private GitHubReleaseFetcher $github,
        private string $currentVersion,
        private LoggerInterface $logger
    ) {}

public function prepare(string $zipPath, string $version): string
{
    $tmpDir = sys_get_temp_dir() . '/keystone_prepare_' . uniqid();
    mkdir($tmpDir);

    $zip = new ZipArchive();
    $zip->open($zipPath);
    $zip->extractTo($tmpDir);
    $zip->close();

    // Geen activate()
    return $tmpDir;
}


    public function check(): array
    {
        $latest = $this->github->latestRelease();

        $latestVersion = ltrim($latest['tag_name'], 'v');

        $hasUpdate = version_compare(
            $latestVersion,
            $this->currentVersion,
            '>'
        );

        $this->logger->info("Checking home for new version",[      
            'has_update' => $hasUpdate,
            'current' => $this->currentVersion,
            'latest' => $latestVersion,
            'release' => $latest
            ]);
        
            return [
            'has_update' => $hasUpdate,
            'current' => $this->currentVersion,
            'latest' => $latestVersion,
            'release' => $latest,
        ];
    }
}

?>