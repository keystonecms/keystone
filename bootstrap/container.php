<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use function DI\factory;
use function DI\autowire;
use function DI\create;

use Keystone\Security\CsrfToken;
use Keystone\Http\Middleware\CsrfMiddleware;
use Keystone\Domain\Page\PagePolicy;
use Keystone\Domain\User\UserPolicy;
use Keystone\Domain\Page\PageRepositoryInterface;
use Keystone\Domain\User\UserRepositoryInterface;
use Keystone\Infrastructure\Persistence\PageRepository;
use Keystone\Infrastructure\Persistence\UserRepository;
use Keystone\Infrastructure\Auth\PasswordHasher;

use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Views\Twig;
use Twig\TwigFunction;
use Twig\Environment;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

use GuzzleHttp\Client;

use Keystone\Core\Theme\ThemeManagerInterface;
use Keystone\Core\Theme\ThemeManager;
use Keystone\Core\Theme\ThemeInstallerInterface;
use Keystone\Core\Theme\ThemeInstaller;
use Keystone\Domain\User\CurrentUser;
use Keystone\Http\Middleware\AuthMiddleware;

use Keystone\Core\Plugin\PluginDiscovery;
use Keystone\Core\Plugin\PluginLoader;
use Keystone\Core\Plugin\PluginRegistry;
use Keystone\Core\Plugin\PluginRepositoryInterface;
use Keystone\Core\Plugin\PluginRepository;

use Keystone\Http\Error\ErrorHandler;
use Keystone\Domain\Menu\Service\LinkResolver;
use Keystone\Domain\Menu\Repository\MenuRepositoryInterface;
use Keystone\Domain\Menu\Repository\MenuWriteRepositoryInterface;
use Keystone\Infrastructure\Persistence\MenuRepository;
use Keystone\Infrastructure\Persistence\MenuWriteRepository;
use Keystone\Core\Settings\SettingsInterface;
use Keystone\Core\Settings\DatabaseSettings;
use Keystone\Infrastructure\Paths;
use Keystone\Core\System\ErrorRepositoryInterface;
use Keystone\Infrastructure\System\ErrorRepository;
use Keystone\Admin\Menu\AdminMenuRegistry;
use Keystone\Http\Middleware\AdminMenuMiddleware;
use Keystone\Security\IpInfo\IpInfoCacheInterface;
use Keystone\Security\IpInfo\NullIpInfoCache;
use Keystone\Security\IpInfo\IpInfoClient;

use Keystone\Domain\Role\RoleRepositoryInterface;
use Keystone\Domain\Policy\PolicyRepositoryInterface;
use Keystone\Infrastructure\Persistence\RoleRepository;
use Keystone\Infrastructure\Persistence\PolicyRepository;
use Keystone\Http\Session\SessionInterface;
use Keystone\Infrastructure\Session\PhpSession;

use Keystone\Core\Plugin\PluginDiscoveryInterface;
use Keystone\Core\Plugin\PluginSyncService;
use Keystone\Core\Plugin\PluginSyncServiceInterface;
use Keystone\Core\Plugin\PluginRegistryInterface;

use Keystone\Core\Mail\MailerInterface;
use Keystone\Core\Mail\NullMailer;

use Keystone\Core\Auth\TwoFactor\TwoFactorHandlerInterface;
use Keystone\Core\Auth\TwoFactor\NullTwoFactorHandler;

use Keystone\Security\LoginAudit\LoginAuditRepositoryInterface;
use Keystone\Security\LoginAudit\LoginAuditRepository;
use Keystone\i18n\Translator;



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

        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($driver === 'mysql') {
            // Forceer UTF-8
            $pdo->exec("SET NAMES utf8mb4");

            // Forceer UTC voor deze database-sessie
            $pdo->exec("SET time_zone = '+00:00'");
        }


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
        }

        return $twig;
    },
    IpInfoClient::class => function ($c) {
        return new IpInfoClient(
            $c->get(Client::class),
            $c->get('settings')['ipinfo']['token']
            );
    },

     Environment::class => function ($container) {
        return $container->get(Twig::class)->getEnvironment();
     },
    CurrentUser::class => factory(function ($c) {
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
    Paths::class => DI\autowire()->constructorParameter('basePath', BASE_PATH),
    // PluginLoader::class => function ($c) {
    //     return new PluginLoader(
    //         $c,
    //         $c->get(\Psr\Log\LoggerInterface::class),
    //         BASE_PATH . '/plugins'
    //     );
    // },
    // PluginDiscovery::class => DI\autowire(),
    PluginLoader::class => DI\autowire(),
    Translator::class => DI\autowire(),
    PasswordHasher::class => DI\create(),
    LinkResolver::class => DI\autowire(),
    ErrorHandler::class => DI\autowire(),
    PluginRegistry::class => DI\create(),
    AuthMiddleware::class => DI\autowire(),
    AdminMenuMiddleware::class => DI\autowire(),
    CsrfToken::class => create(),
    CsrfMiddleware::class => DI\autowire(),

/**
 * autowire statements 
 */
   PluginRegistryInterface::class => DI\autowire(PluginRegistry::class),
   PluginSyncServiceInterface::class => DI\autowire(PluginSyncService::class),
   PluginDiscoveryInterface::class => DI\autowire(PluginDiscovery::class),
   MailerInterface::class => DI\autowire(NullMailer::class),
   TwoFactorHandlerInterface::class => DI\autowire(NullTwoFactorHandler::class),
   PluginRepositoryInterface::class => DI\autowire(PluginRepository::class),
   LoginAuditRepositoryInterface::class => DI\autowire(LoginAuditRepository::class),
   IpInfoCacheInterface::class => DI\autowire(NullIpInfoCache::class),
   AdminMenuRegistry::class => DI\create(AdminMenuRegistry::class),
   ErrorRepositoryInterface::class => DI\autowire(ErrorRepository::class),
   SessionInterface::class => DI\autowire(PhpSession::class),
   RoleRepositoryInterface::class => DI\autowire(RoleRepository::class),
   PolicyRepositoryInterface::class => DI\autowire(PolicyRepository::class),
   PageRepositoryInterface::class => DI\autowire(PageRepository::class),
   ThemeManagerInterface::class => DI\autowire(ThemeManager::class),
   ThemeInstallerInterface::class => DI\autowire(ThemeInstaller::class),
   SettingsInterface::class => DI\autowire(DatabaseSettings::class),
   ResponseFactoryInterface::class => create(ResponseFactory::class),
   CurrentUser::class => create(), // request-scoped
   UserRepositoryInterface::class => DI\autowire(UserRepository::class),
   MenuRepositoryInterface::class => DI\autowire(MenuRepository::class),
   MenuWriteRepositoryInterface::class => DI\autowire(MenuWriteRepository::class),

];
?>