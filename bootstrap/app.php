<?php

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use Psr\Log\LoggerInterface;

use Keystone\Core\Setup\SetupConfig;
use Keystone\Http\Error\ErrorHandler;

use Keystone\Http\Middleware\SessionMiddleware;
use Keystone\Http\Middleware\CurrentUserMiddleware;
use Keystone\Http\Middleware\DomainLocaleMiddleware;

use Keystone\Core\Plugin\PluginDiscoveryInterface;
use Keystone\Core\Plugin\PluginSyncServiceInterface;
use Keystone\Core\Plugin\PluginLoader;

use Keystone\I18n\LocaleContext;
use Keystone\I18n\DomainLocaleResolver;
use Keystone\I18n\Translator;

use Keystone\Core\Migration\MigrationRunner;
use Keystone\Core\Migration\MigrationRepository;

use Keystone\Http\Middleware\InstallerGuardMiddleware;

require BASE_PATH . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Start sessie
|--------------------------------------------------------------------------
*/
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| SetupConfig (altijd beschikbaar)
|--------------------------------------------------------------------------
*/
$setup = new SetupConfig(
    envPath: BASE_PATH . '/config/.env',
    lockFilePath: BASE_PATH . '/installed.lock',
    migrationPath: BASE_PATH . '/database/migrations'
);

/*
|--------------------------------------------------------------------------
| INSTALLER MODUS
|--------------------------------------------------------------------------
*/
if (!$setup->isInstalled()) {


    $builder = new ContainerBuilder();
    $builder->addDefinitions(
        require BASE_PATH . '/bootstrap/container.install.php'
    );




    $container = $builder->build();

    AppFactory::setContainer($container);
    $app = AppFactory::create();


    // installer middleware (minimaal)
    $app->addBodyParsingMiddleware();

    $app->addRoutingMiddleware();

    $app->add(InstallerGuardMiddleware::class);

    // installer routes
    require BASE_PATH . '/app/Http/Routes/install.php';

    return $app;
}

/*
|--------------------------------------------------------------------------
| APP MODUS
|--------------------------------------------------------------------------
*/
if (!defined('KEYSTONE_VERSION')) {
    define('KEYSTONE_VERSION', '1.0.0');
}

// env pas laden NA installatie
$dotenv = Dotenv::createImmutable(BASE_PATH . '/config');
$dotenv->safeLoad();

$builder = new ContainerBuilder();
$builder->addDefinitions(
    require BASE_PATH . '/bootstrap/container.php'
);






$container = $builder->build();

$container->set(
    'settings',
    fn () => require BASE_PATH . '/config/settings.php'
);

$container->set(LocaleContext::class, function () {
    return new LocaleContext($_ENV['DEFAULT_LOCALE'] ?? 'en_US');
});

$container->set(DomainLocaleResolver::class, function ($c) {
    return new DomainLocaleResolver($c->get('settings')['i18n']['domains']);
});

$container->set(MigrationRunner::class, function ($c) {
    return new MigrationRunner(
        $c->get(PDO::class),
        $c->get(MigrationRepository::class),
        $c->get(LoggerInterface::class),
    );
});


AppFactory::setContainer($container);
$app = AppFactory::create();

// middleware (zoals je al had)
$app->add(CurrentUserMiddleware::class);
$app->addBodyParsingMiddleware();
$app->add(SessionMiddleware::class);
$app->add(DomainLocaleMiddleware::class);
$app->addRoutingMiddleware();


// --------------------------------------------------
// 9. LOAD PLUGINS (Pages, later Blog, etc.)
// --------------------------------------------------
$discovery = $container->get(PluginDiscoveryInterface::class);
$descriptors = $discovery->discover();

$container
    ->get(PluginSyncServiceInterface::class)
    ->sync($descriptors);

$container
    ->get(PluginLoader::class)
    ->load($app, $descriptors);


// error handling
$errorMiddleware = $app->addErrorMiddleware(
    ($_ENV['APP_DEBUG'] ?? '0') === '1',
    true,
    true
);


$errorMiddleware->setDefaultErrorHandler(
    $container->get(ErrorHandler::class)
);

// normale routes
require BASE_PATH . '/app/Http/Routes/auth.php';

require BASE_PATH . '/bootstrap/twig.php';
require BASE_PATH . '/app/Http/Routes/system.php';
require BASE_PATH . '/app/Http/Routes/themes.php';
require BASE_PATH . '/app/Http/Routes/roles.php';
require BASE_PATH . '/app/Http/Routes/admin.php';
require BASE_PATH . '/app/Http/Routes/account.php';
require BASE_PATH . '/app/Http/Routes/public.php';

return $app;


?>