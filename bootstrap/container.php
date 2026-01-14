<?php

declare(strict_types=1);

use DI\ContainerBuilder;
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

return [

    PDO::class => function () {
        return new PDO(
            $_ENV['DB_DSN'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASS'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    },
    Twig::class => function (ContainerInterface $c) {
        static $twig = null;

        if ($twig === null) {
            $twig = Twig::create(
                dirname(__DIR__) . '/templates',
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
        }

        return $twig;
    },


    /**
     * Logger interface to file for now
     */
    LoggerInterface::class => function () {

        $logger = new Logger('keystone');

        $logger->pushProcessor(new UidProcessor());

        // Algemene applicatie logs
            $logger->pushHandler(
                new StreamHandler(
                    __DIR__ . '/../storage/logs/app.log',
                    Logger::INFO
                )
            );

            $logger->pushHandler(
                new StreamHandler(
                    __DIR__ . '/../storage/logs/error.log',
                    Logger::ERROR
                )
            );

        return $logger;
    },
    PluginLoader::class => function ($c) {
        return new PluginLoader(
            $c,
            $c->get(\Psr\Log\LoggerInterface::class),
            __DIR__ . '/../plugins'
        );
    },
    PasswordHasher::class => DI\create(),

    PageRepositoryInterface::class => DI\autowire(PageRepository::class),
    UserRepositoryInterface::class => DI\autowire(UserRepository::class),

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

    // Authorizer::class => autowire(),
    AuthMiddleware::class => autowire(),
    CsrfToken::class => create(),
    CsrfMiddleware::class => autowire(),
/**
 * autowire statements
 */
   ResponseFactoryInterface::class => create(ResponseFactory::class),
   CurrentUser::class => create(), // request-scoped
   UserRepositoryInterface::class => autowire(UserRepository::class),

];

?>