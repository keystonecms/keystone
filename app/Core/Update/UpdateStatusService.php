<?php

declare(strict_types=1);

namespace Keystone\Core\Update;

use Psr\SimpleCache\CacheInterface;
use Keystone\Core\Update\VersionReader;
use Keystone\Core\Update\UpdateSource;

final class UpdateStatusService {

    public function __construct(
        private VersionReader $versionReader,
        private UpdateSource $updateSource,
    ) {}

    public function getStatus(): UpdateStatus
    {

    $current = $this->versionReader->current();

    $latest  = $this->updateSource->latestVersion();

    return new UpdateStatus(
            current: $current,
            latest: $latest,
            hasUpdate: version_compare($latest, $current, '>')
        );
    }
}




// final class UpdateStatusService {

//     public function __construct(
//         private readonly VersionReader $versionReader,
//         private readonly UpdateSource $updateSource,
//         private readonly CacheInterface $cache
//     ) {}

//     public function getStatus(): UpdateStatus {
//         return $this->cache->get('keystone.update.status')
//             ?? $this->refresh();
//     }

//     private function refresh(): UpdateStatus {
//         $current = $this->versionReader->current();
//         $latest  = $this->updateSource->latestVersion();

//         $status = new UpdateStatus(
//             current: $current,
//             latest: $latest,
//             hasUpdate: version_compare($latest, $current, '>')
//         );

//         $this->cache->set(
//             'keystone.update.status',
//             $status,
//             3600 // 1 uur
//         );

//         return $status;
//     }
// }


?>