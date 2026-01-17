<?php

declare(strict_types=1);

namespace Keystone\Plugins\System\Infrastructure\GitHub;

use RuntimeException;

final class GitHubReleaseFetcher {
    
    public function __construct(
        private string $token,
        private string $repository
    ) {}

    public function latestRelease(): array
    {
        $url = sprintf(
            'https://api.github.com/repos/%s/releases/latest',
            $this->repository
        );

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/vnd.github+json',
                'Authorization: Bearer ' . $this->token,
                'User-Agent: Keystone-Updater',
            ],
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            throw new RuntimeException('GitHub API request failed');
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status !== 200) {
            throw new RuntimeException(
                'GitHub API returned status ' . $status
            );
        }

        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }
}

?>