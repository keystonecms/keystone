<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use function DI\factory;
use function DI\autowire;
use function DI\create;
use Keystone\Security\CsrfToken;
use Keystone\Http\Middleware\CsrfMiddleware;
// use Keystone\Core\Authorizer\Authorizer;
use Keystone\Core\Authorizer\PolicyResolver;
use Keystone\Domain\Page\PagePolicy;
use Keystone\Domain\User\UserPolicy;
use Keystone\Domain\Page\PageRepositoryInterface;
use Keystone\Domain\User\UserRepositoryInterface;
use Keystone\Infrastructure\Persistence\PageRepository;
use Keystone\Infrastructure\Persistence\UserRepository;
use Keystone\Infrastructure\Auth\PasswordHasher;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Twig\TwigFunction;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

use Keystone\Domain\User\CurrentUser;
use Keystone\Http\Middleware\AuthMiddleware;
use Keystone\Core\Plugin\PluginLoader;
use Keystone\Core\Auth\Authorizer;
use Keystone\Core\Plugin\PluginRegistry;
use Keystone\Http\Error\ErrorHandler;

return [
    PDO::class => function () {
        $pdo = new PDO(
            $_ENV['DB_DSN'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASS'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );

        // 🔒 Forceer UTC voor deze database-sessie
        $pdo->exec("SET time_zone = '+00:00'");

        return $pdo;
    },
    Twig::class => function (ContainerInterface $c) {
        static $twig = null;

        if ($twig === null) {
            $twig = Twig::create(
                BASE_PATH . '/templates',
                ['cache' => false]
            );

            $csrf = $c->get(\Keystone\Security\CsrfToken::class);

            $twig->getEnvironment()->addFunction(
                new \Twig\TwigFunction(
                    'csrf',
                    fn () => sprintf(
                        '<input type="hidden" name="_csrf_token" value="%s">',
                        htmlspecialchars($csrf->generate(), ENT_QUOTES)
                    ),
                    ['is_safe' => ['html']]
                )
            );
                $twig->getLoader()->addPath(
                BASE_PATH . '/templates',
                'core'
            );
            // Keystone globals
           $twig->getEnvironment()->addGlobal('keystone', ['version' => KEYSTONE_VERSION,
            ]);
            }

        return $twig;
    },
    CurrentUser::class => factory(function ($c) {
      error_log('CurrentUser factory called');
    if (!isset($_SESSION['user_id'])) {
        return new CurrentUser(null);
    }

      $userRepository = $c->get(UserRepositoryInterface::class);

      $user = $userRepository->findById((int) $_SESSION['user_id']);

    return new CurrentUser($user);
}),
    /**
     * Logger interface to file for now
     */
    LoggerInterface::class => function () {

        $logger = new Logger('keystone');

        $logger->pushProcessor(new UidProcessor());

        // Algemene applicatie logs
            $logger->pushHandler(
                new StreamHandler(
                    BASE_PATH . '/storage/logs/app.log',
                    Logger::INFO
                )
            );

            $logger->pushHandler(
                new StreamHandler(
                    BASE_PATH . '/storage/logs/error.log',
                    Logger::ERROR
                )
            );

        return $logger;
    },
    PluginLoader::class => function ($c) {
        return new PluginLoader(
            $c,
            $c->get(\Psr\Log\LoggerInterface::class),
            BASE_PATH . '/plugins'
        );
    },
    PasswordHasher::class => DI\create(),

    PageRepositoryInterface::class => DI\autowire(PageRepository::class),

    PolicyResolver::class => function () {
        $resolver = new PolicyResolver();

        $resolver->register('page.view', PagePolicy::class);
        $resolver->register('page.create', PagePolicy::class);
        $resolver->register('page.update', PagePolicy::class);
        $resolver->register('page.delete', PagePolicy::class);
        $resolver->register('page.publish', PagePolicy::class);

        $resolver->register('user.manage', UserPolicy::class);

        return $resolver;
    },
    Authorizer::class => function () {
    return new Authorizer();
    },
    
    ErrorHandler::class => DI\autowire(),
    PluginRegistry::class => DI\create(),
    // Authorizer::class => autowire(),
    AuthMiddleware::class => DI\autowire(),
    CsrfToken::class => create(),
    CsrfMiddleware::class => Di\autowire(),
/**
 * autowire statements
 */
   ResponseFactoryInterface::class => create(ResponseFactory::class),
   CurrentUser::class => create(), // request-scoped
   UserRepositoryInterface::class => autowire(UserRepository::class),

];

?>