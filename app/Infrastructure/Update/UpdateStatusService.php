<?php

declare(strict_types=1);

namespace Keystone\Infrastructure\Update;

final class UpdateStatusService {
    public function __construct(
        private readonly UpdateSource $source,
        private readonly VersionReader $versionReader
    ) {}

    public function getStatus(): UpdateStatus
    {
        $current = $this->versionReader->current();
        $latest  = $this->source->latestVersion();

        return new UpdateStatus(
            current: $current,
            latest: $latest,
            hasUpdate: version_compare($latest, $current, '>')
        );
    }
}

?>