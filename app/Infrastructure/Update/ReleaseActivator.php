<?php

declare(strict_types=1);

namespace Keystone\Infrastructure\Update;

use RuntimeException;

final class ReleaseActivator {
    public function activate(
        string $version,
        string $extractedPath,
        string $projectRoot
    ): void {
        $releasesDir = $projectRoot . '/releases';
        $targetDir   = $releasesDir . '/' . $version;
        $currentLink = $projectRoot . '/current';

        if (!is_dir($releasesDir)) {
            mkdir($releasesDir, 0755, true);
        }

        // 1. Move extracted release into releases/
        if (is_dir($targetDir)) {
            throw new RuntimeException("Release $version already exists");
        }

        rename($extractedPath, $targetDir);

        // 2. Prepare shared symlinks
        foreach (['config', 'storage', 'uploads'] as $shared) {
            $link = $targetDir . '/' . $shared;
            if (!is_link($link)) {
                symlink("../../shared/$shared", $link);
            }
        }

        // 3. Atomische switch
        $tmpLink = $projectRoot . '/.current_tmp';

        symlink("releases/$version", $tmpLink);
        rename($tmpLink, $currentLink);
    }
}

?>