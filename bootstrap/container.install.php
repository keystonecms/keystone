<?php

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Slim\Views\Twig;
use Twig\Loader\FilesystemLoader;
use Keystone\Core\Setup\SetupConfig;
use Keystone\Core\Setup\InstallerKernel;

use Keystone\Core\Setup\Step\CheckEnvironmentStep;
use Keystone\Core\Setup\Step\DatabaseSetupStep;
use Keystone\Core\Setup\Step\MigrationStep;
use Keystone\Core\Setup\Step\AdminUserStep;
use Keystone\Core\Setup\Step\FinalizeStep;

use Keystone\Core\Setup\System\PhpVersionChecker;
use Keystone\Core\Setup\System\PhpVersionCheckerInterface;
use Keystone\Core\Setup\System\PhpExtensionChecker;
use Keystone\Core\Setup\System\PhpExtensionCheckerInterface;
use Keystone\Core\Setup\System\WritablePathChecker;
use Keystone\Core\Setup\System\WritablePathCheckerInterface;
use Keystone\Core\Setup\Database\MigrationRunnerInterface;
use Keystone\Core\Migration\InstallerMigrationRunner;
use Keystone\Core\Migration\MigrationRunner;
use Keystone\Core\Setup\InstallerState;
use Keystone\Core\Setup\Env\EnvWriterInterface;
use Keystone\Core\Setup\Env\EnvFileWriter;
use Keystone\Core\Migration\MigrationProvider;
use Keystone\Core\Migration\MigrationRepository;
/*
|--------------------------------------------------------------------------
| Installer container
|--------------------------------------------------------------------------
*/
return [
    /*
    |--------------------------------------------------------------------------
    | Env writer locatie
    |--------------------------------------------------------------------------
    */
    // EnvWriterInterface::class => function () {
    //     return new EnvFileWriter(
    //         BASE_PATH . 'config/.env'
    //     );
    // },
    /*
    |--------------------------------------------------------------------------
    | Setup config
    |--------------------------------------------------------------------------
    */
    SetupConfig::class => fn () =>
        new SetupConfig(
            envPath: BASE_PATH . '/config/.env',
            lockFilePath: BASE_PATH . '/installed.lock',
            migrationPath: BASE_PATH . '/database/migrations'
        ),

    /*
    |--------------------------------------------------------------------------
    | Logger (stil tijdens install)
    |--------------------------------------------------------------------------
    */
    LoggerInterface::class => fn () => new NullLogger(),

    /*
    |--------------------------------------------------------------------------
    | Twig (installer UI)
    |--------------------------------------------------------------------------
    */
    FilesystemLoader::class => fn () =>
        new FilesystemLoader(
            BASE_PATH . '/templates/installer'
        ),

    Twig::class => fn (FilesystemLoader $loader) =>
        new Twig($loader, [
            'cache' => false,
        ]),

    /*
    |--------------------------------------------------------------------------
    | Installer steps
    |--------------------------------------------------------------------------
    */
    CheckEnvironmentStep::class => DI\autowire(),
    DatabaseSetupStep::class    => DI\autowire(),
    MigrationStep::class        => DI\autowire(),
    AdminUserStep::class        => DI\autowire(),
    FinalizeStep::class         => DI\autowire(),

    MigrationRepository::class => fn () => new MigrationRepository(),

    MigrationRunner::class => fn ($c) => new MigrationRunner(
        $c->get(MigrationRepository::class),
        $c->get(LoggerInterface::class),
    ),

    MigrationRepository::class => fn () => new MigrationRepository(),
    InstallerState::class => fn () => new InstallerState(),
    MigrationProvider::class => fn () => new MigrationProvider([]),

    EnvWriterInterface::class => DI\autowire(EnvFileWriter::class),
    PhpVersionCheckerInterface::class => DI\autowire(PhpVersionChecker::class),
    PhpExtensionCheckerInterface::class => DI\autowire(PhpExtensionChecker::class),
    WritablePathCheckerInterface::class => DI\autowire(WritablePathChecker::class),
    MigrationRunnerInterface::class => DI\autowire(InstallerMigrationRunner::class),
    /*
    |--------------------------------------------------------------------------
    | Installer kernel
    |--------------------------------------------------------------------------
    */
    InstallerKernel::class => fn ($c) => new InstallerKernel([
        $c->get(CheckEnvironmentStep::class),
        $c->get(DatabaseSetupStep::class),
        $c->get(MigrationStep::class),
        $c->get(AdminUserStep::class),
        $c->get(FinalizeStep::class)
    ]),

// MigrationProvider::class => fn () => new MigrationProvider([
//     // Core migrations
//     \Keystone\Core\Migration\Migrations\CreateUsersTable::class,
//     \Keystone\Core\Migration\Migrations\CreatePagesTable::class,


//     // Plugins later
//     ]),

];



?>