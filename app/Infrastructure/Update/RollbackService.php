<?php

declare(strict_types=1);

namespace Keystone\Infrastructure\Update;

use RuntimeException;

final class RollbackService {

public function rollback(string $projectRoot): void
    {
        $releasesDir = $projectRoot . '/releases';
        $currentLink = $projectRoot . '/current';

        $releases = array_values(array_filter(
            scandir($releasesDir),
            fn ($d) => $d[0] !== '.'
        ));

        if (count($releases) < 2) {
            throw new RuntimeException('No previous release to roll back to');
        }

        sort($releases);
        $previous = $releases[count($releases) - 2];

        $tmpLink = $projectRoot . '/.current_tmp';
        symlink("releases/$previous", $tmpLink);
        rename($tmpLink, $currentLink);
    }
}


?>