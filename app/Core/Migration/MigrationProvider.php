<?php

declare(strict_types=1);

namespace Keystone\Core\Migration;

use Keystone\Core\Migration\MigrationRepository;
use PDO;

final class MigrationProvider {
    /**
     * @var array<class-string>
     */
    private array $migrationClasses;

    public function __construct(
        array $migrationClasses
        ) {
        $this->migrationClasses = [
            \Keystone\Core\Migration\Migrations\CreateMigrationsTable::class,
            \Keystone\Core\Migration\Migrations\CreateUsersTable::class,
            \Keystone\Core\Migration\Migrations\CreatePagesTable::class,
            \Keystone\Core\Migration\Migrations\CreateRolesTable::class,
            \Keystone\Core\Migration\Migrations\CreateUserRoleTable::class,
            \Keystone\Core\Migration\Migrations\CreatePluginsTable::class,
            \Keystone\Core\Migration\Migrations\CreateActivityLogTable::class,
            \Keystone\Core\Migration\Migrations\CreateSettingsTable::class,
            \Keystone\Core\Migration\Migrations\CreateSystemErrorsTable::class,
            \Keystone\Core\Migration\Migrations\CreateSecurityEventsTable::class,
            \Keystone\Core\Migration\Migrations\SeedDefaultRoles::class,
            \Keystone\Core\Migration\Migrations\SeedInitialSettings::class,
            \Keystone\Core\Migration\Migrations\CreateUserTokensTable::class,
            \Keystone\Core\Migration\Migrations\CreateLoginAuditTable::class,
            \Keystone\Core\Migration\Migrations\CreatePolicyTable::class,        
            \Keystone\Core\Migration\Migrations\CreateMenuTable::class, 
            \Keystone\Core\Migration\Migrations\CreateMenuItemsTable::class, 
            \Keystone\Core\Migration\Migrations\CreateRolesPolicyTable::class, 
            \Keystone\Core\Migration\Migrations\CreateUserSecuritySettingsTable::class,

        ];
    }

    /**
     * @return array<object> All migration instances
     */
    public function all(): array {

        return array_map(
            fn (string $class) => new $class(),
            $this->migrationClasses
        );
    }

    /**
     * @return array<object> Only migrations not yet executed
     */
    public function pending(PDO $pdo, MigrationRepository $repository): array {
        $pending = [];

        foreach ($this->all() as $migration) {
            if (!$repository->hasRun(
                $pdo,
                $migration->getPlugin(),
                $migration->getVersion()
            )) {
                $pending[] = $migration;
            }
        }

        return $pending;
    }
}



?>