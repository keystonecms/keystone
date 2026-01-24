<?php

namespace Keystone\Core\Dashboard;

use DateTimeImmutable;

final class DashboardActivity {
    
    public function __construct(
        public readonly string $message,
        public readonly DateTimeImmutable $occurredAt,
    ) {}
}


?>