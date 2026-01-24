<?php

namespace Keystone\Core\Auth;

use Keystone\Core\Auth\PolicyRepositoryInterface;

final class PolicySeeder {
    public function __construct(
        private PolicyRepositoryInterface $policies,
    ) {}

    public function seed(array $definitions): void {
        foreach ($definitions as $key => $def) {
            
            $this->policies->findOrCreate(
                key: $key,
                label: $def['label'],
                category: $def['category']
            );
        }
    }
}

?>
