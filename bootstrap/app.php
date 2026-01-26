<?php

declare(strict_types=1);

/*
 * Keystone CMS
 *
 * @author Constan van Suchtelen van de Haere <constan.vansuchtelenvandehaere@hostingbe.com>
 * @copyright 2026 HostingBE
 * @package   Keystone CMS
 * @author    HostingBE
 * @license   MIT
 * @link      https://keystone-cms.com
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
 * files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy,
 * modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software
 * is furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
 * BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF
 * OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use Psr\Log\LoggerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Keystone\Core\Http\Exception\NotFoundException as CmsNotFoundException;
use Slim\Exception\HttpNotFoundException;
use Keystone\Core\Http\Exception\ForbiddenException;
use Keystone\Http\Middleware\SessionMiddleware;
use Keystone\Http\Middleware\CurrentUserMiddleware;
use Keystone\Http\Middleware\DomainLocaleMiddleware;
use Keystone\Http\Error\ErrorHandler;
use Keystone\Core\Setup\InstallerKernel;
use Keystone\Core\Setup\Step;
use Keystone\Domain\Menu\Service\LinkResolver;
use Keystone\Domain\User\CurrentUser;
use Keystone\Http\Middleware\RequirePolicy;
use Keystone\Core\Auth\PolicyResolver;

use Keystone\Core\Plugin\PluginDiscoveryInterface;
use Keystone\Core\Plugin\PluginSyncServiceInterface;
use Keystone\Core\Plugin\PluginLoader;

use Keystone\I18n\LocaleContext;
use Keystone\I18n\DomainLocaleResolver;
use Keystone\I18n\Translator;

use Throwable;
// --------------------------------------------------
// 1. Composer autoload
// --------------------------------------------------
require BASE_PATH . '/vendor/autoload.php';

// --------------------------------------------------
// 2. Environment (.env)
// --------------------------------------------------
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH . '/config');
$dotenv->safeLoad();

// --------------------------------------------------
// 4. Build DI container
// --------------------------------------------------
$containerBuilder = new ContainerBuilder();

// In productie: enable compilation
// $containerBuilder->enableCompilation(__DIR__ . '/../var/cache');

$containerBuilder->addDefinitions(
    require BASE_PATH . '/bootstrap/container.php'
);



$container = $containerBuilder->build();

$container->set(
    'settings',
    fn () => require BASE_PATH . '/config/settings.php'
);

$container->set(
    Keystone\Core\Setup\SetupConfig::class,
    new Keystone\Core\Setup\SetupConfig(
        envPath: BASE_PATH . '/.env',
        lockFilePath: BASE_PATH . '/installed.lock',
        migrationPath: BASE_PATH . '/database/migrations'
    )
);

$container->set(LocaleContext::class, function () {
    return new LocaleContext($_ENV['DEFAULT_LOCALE'] ?? 'en_US');
});

$container->set(DomainLocaleResolver::class, function ($c) {
    return new DomainLocaleResolver($c->get('settings')['i18n']['domains']);
});

$container->set(InstallerKernel::class, function ($c) {
    return new InstallerKernel([
        $c->get(Step\CheckEnvironmentStep::class),
        $c->get(Step\DatabaseSetupStep::class),
        $c->get(Step\MigrationStep::class),
        $c->get(Step\AdminUserStep::class),
        $c->get(Step\FinalizeStep::class),
    ]);
});

$container->set(UpdaterKernel::class, function ($c) {
    return new UpdaterKernel(
        new SetupKernel([
            $c->get(\Keystone\Core\Setup\Step\CheckEnvironmentStep::class),
            $c->get(\Keystone\Core\Setup\Step\MigrationStep::class),
        ])
    );
});

$container->set(
    RequirePolicy::class,
    fn ($c) => new RequirePolicy(
        $c->get(PolicyResolver::class),
        $c->get(CurrentUser::class),
        $c->get(LoggerInterface::class)
    )
);

// --------------------------------------------------
// 5. Create Slim App
// --------------------------------------------------
AppFactory::setContainer($container);
$app = AppFactory::create();

/** @var LinkResolver $linkResolver */
$linkResolver = $container->get(LinkResolver::class);

/**
 * Externe URLs (literal)
 */
$linkResolver->register('url', function ($item) {
    return $item->linkTarget();
});

/**
 * Interne Slim routes (by name)
 */
$linkResolver->register('route', function ($item) use ($app) {
    try {
        return $app
            ->getRouteCollector()
            ->getRouteParser()
            ->urlFor($item->linkTarget());
    } catch (\Throwable) {
        return '#';
    }
});

//
// Define the used version of Keystone CMS
//
$manifestPath = BASE_PATH . '/manifest.json';

if (!is_file($manifestPath)) {
    throw new RuntimeException('manifest.json ontbreekt');
}

$manifest = json_decode(
    file_get_contents($manifestPath),
    true,
    512,
    JSON_THROW_ON_ERROR
);

if (!defined('KEYSTONE_VERSION')) {
    define('KEYSTONE_VERSION', '1.0.0');
}
// --------------------------------------------------
// 3. Start session (VOOR alles wat auth / csrf doet)
// --------------------------------------------------
$app->add(CurrentUserMiddleware::class);

$app->addBodyParsingMiddleware();

$app->add(SessionMiddleware::class);

$app->add(DomainLocaleMiddleware::class);

// --------------------------------------------------
// 6. Routing middleware (verplicht in Slim 4)
// --------------------------------------------------
$app->addRoutingMiddleware();

// --------------------------------------------------
// 7. Error middleware
// --------------------------------------------------
$displayErrors = ($_ENV['APP_DEBUG'] ?? '0') === '1';

$errorMiddleware = $app->addErrorMiddleware(
    $displayErrors,
    true,
    true
);

/*
|--------------------------------------------------------------------------
| 3. 500 — echte fouten (ERROR)
|--------------------------------------------------------------------------
*/
$errorMiddleware->setDefaultErrorHandler(
    $container->get(ErrorHandler::class)
);


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


// --------------------------------------------------
// 8. CORE ROUTES (meest specifiek)
// --------------------------------------------------
require BASE_PATH . '/bootstrap/twig.php';
// Auth routes (login/logout)
require BASE_PATH . '/app/Http/Routes/auth.php';

// --------------------------------------------------
// 8. CATCH-ALL ROUTES (ALTIJD LAATST)
// --------------------------------------------------
require BASE_PATH . '/app/Http/Routes/system.php';
require BASE_PATH . '/app/Http/Routes/themes.php';
require BASE_PATH . '/app/Http/Routes/roles.php';
require BASE_PATH . '/app/Http/Routes/admin.php';
require BASE_PATH . '/app/Http/Routes/account.php';
require BASE_PATH . '/app/Http/Routes/public.php';

return $app;
?>