<?php

declare(strict_types=1);

namespace Keystone\Plugins\System\Infrastructure\Update;

use RuntimeException;

final class ReleaseDownloader {


    public function __construct(
        private string $githubToken
    ) {}

    public function download(string $zipUrl): string
    {
        $tmpFile = sys_get_temp_dir()
            . '/keystone_release_'
            . uniqid()
            . '.zip';

        $fp = fopen($tmpFile, 'w');

        if ($fp === false) {
            throw new RuntimeException('Unable to create temp file');
        }

        $ch = curl_init($zipUrl);

        curl_setopt_array($ch, [
            CURLOPT_FILE => $fp,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->githubToken,
                'User-Agent: Keystone-Updater',
                'Accept: application/vnd.github+json',
            ],
        ]);

        $ok = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        fclose($fp);

        if ($ok === false || $status !== 200) {
            @unlink($tmpFile);
            throw new RuntimeException(
                'Failed to download release ZIP (HTTP ' . $status . ')'
            );
        }

        return $tmpFile;
    }
}

?>