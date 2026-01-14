<?php

declare(strict_types=1);

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

use Throwable;
// --------------------------------------------------
// 1. Composer autoload
// --------------------------------------------------
require __DIR__ . '/../vendor/autoload.php';

// --------------------------------------------------
// 2. Environment (.env)
// --------------------------------------------------
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();


// --------------------------------------------------
// 4. Build DI container
// --------------------------------------------------
$containerBuilder = new ContainerBuilder();

// In productie: enable compilation
// $containerBuilder->enableCompilation(__DIR__ . '/../var/cache');

$containerBuilder->addDefinitions(
    require __DIR__ . '/container.php'
);

$container = $containerBuilder->build();

// --------------------------------------------------
// 5. Create Slim App
// --------------------------------------------------
AppFactory::setContainer($container);
$app = AppFactory::create();

// --------------------------------------------------
// 3. Start session (VOOR alles wat auth / csrf doet)
// --------------------------------------------------
$app->add(CurrentUserMiddleware::class);

$app->add(SessionMiddleware::class);
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

$errorMiddleware->setDefaultErrorHandler(
    function (
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails
    ) use ($container) {

        $twig = $container->get(Slim\Views\Twig::class);
        $logger = $container->get(Psr\Log\LoggerInterface::class);
        $responseFactory = $container->get(
            Psr\Http\Message\ResponseFactoryInterface::class
        );

        /*
         |--------------------------------------------------------------------------
         | 1. 404 — route bestaat niet (GEEN error)
         |--------------------------------------------------------------------------
         */
    if ($exception instanceof CmsNotFoundException) {
        return $twig->render(
        $responseFactory->createResponse(404),
        'errors/error.twig',
        [
            'status'  => 404,
            'code'    => 'E404',
            'message' => 'De gevraagde pagina bestaat niet.',
        ]
    );
}

        /*
         |--------------------------------------------------------------------------
         | 2. 403 — wel route, geen rechten (WARN)
         |--------------------------------------------------------------------------
         */
        if ($exception instanceof ForbiddenException) {
            $logger->warning($exception->getMessage(), [
                'path' => (string) $request->getUri(),
            ]);

            return $twig->render(
                $responseFactory->createResponse(403),
                'errors/error.twig',
                [
                    'status'  => 403,
                    'code'    => 'E403',
                    'message' => 'Je hebt geen rechten om deze actie uit te voeren.',
                ]
            );
        }

        /*
         |--------------------------------------------------------------------------
         | 3. 500 — echte fouten (ERROR)
         |--------------------------------------------------------------------------
         */
        $logger->error($exception->getMessage(), [
            'exception' => $exception,
            'path'      => (string) $request->getUri(),
            'method'    => $request->getMethod(),
        ]);

        return $twig->render(
            $responseFactory->createResponse(500),
            'errors/error.twig',
            [
                'status'  => 500,
                'code'    => 'E500',
                'message' => 'Er is iets misgegaan. Probeer het later opnieuw.',
                'debug'   => $displayErrorDetails
                    ? $exception->getMessage()
                    : null,
            ]
        );
    }
);




// --------------------------------------------------
// 8. CORE ROUTES (meest specifiek)
// --------------------------------------------------

// Admin dashboard redirect (optioneel)
$app->get('/admin', function ($request, $response) {
    return $response
        ->withHeader('Location', '/admin/pages')
        ->withStatus(302);
});

// Auth routes (login/logout)
require __DIR__ . '/../app/Http/Routes/auth.php';

// --------------------------------------------------
// 9. LOAD PLUGINS (Pages, later Blog, etc.)
// --------------------------------------------------
$container->get(\Keystone\Core\Plugin\PluginLoader::class)->load($app);

// --------------------------------------------------
// 10. PUBLIC CORE ROUTES (ALLEEN niet-catch-all)
// --------------------------------------------------
// (bijv. homepage, health check, etc.)

// $app->get('/', ...);

// --------------------------------------------------
// 11. CATCH-ALL ROUTES (ALTIJD LAATST)
// --------------------------------------------------
require __DIR__ . '/../app/Http/Routes/public.php';
// --------------------------------------------------
// 12. Run application
// --------------------------------------------------


// GEEN $app->run() hier
return $app;


?>