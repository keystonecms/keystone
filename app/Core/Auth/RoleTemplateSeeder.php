<?php

namespace Keystone\Core\Auth;

use Keystone\Core\Auth\RoleRepositoryInterface;
use Keystone\Core\Auth\PolicyRepositoryInterface;

final class RoleTemplateSeeder {

    public function __construct(
        private RoleRepositoryInterface $roles,
        private PolicyRepositoryInterface $policies,
    ) {}

    public function seed(array $templates): void {
        foreach ($templates as $name => $template) {

            $roleId = $this->roles->findOrCreate(
                name: $name,
                label: $template['label']
            );

            if (in_array('*', $template['policies'], true)) {
                $policyIds = $this->policies->allIds();
            } else {
                $policyIds = $this->policies
                    ->idsByKeys($template['policies']);
            }

            $this->roles->syncPolicies($roleId, $policyIds);
        }
    }
}


?>