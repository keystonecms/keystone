<?php

declare(strict_types=1);

namespace Keystone\Infrastructure\Update;

final readonly class UpdateStatus {
    
    public function __construct(
        public string $current,
        public string $latest,
        public bool $hasUpdate
    ) {}
}


?>