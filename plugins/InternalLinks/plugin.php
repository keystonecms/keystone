<?php

use function DI\autowire;
use Keystone\Core\Plugin\PluginInterface;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Views\Twig;

use Keystone\Core\Auth\Authorizer;
use Keystone\Plugins\InternalLinks\Domain\{
    InternalLinkRepositoryInterface,
    InternalLink,
    LinkSubject,
    InternalLinkService,
    InternalLinkPolicy
};

use Keystone\Plugins\InternalLinks\Infrastructure\Persistence\PdoInternalLinkRepository;

return new class implements PluginInterface {

    public function getName(): string {
        return 'Internal Links';
    }

    public function getVersion(): string {
        return 'v1.0.0';
    }

    public function getDescription(): string {
        return 'Internal Links';
    }

    public function register(ContainerInterface $container): void
    {
        // repository
        $container->set(
            InternalLinkRepositoryInterface::class,
            autowire(PdoInternalLinkRepository::class)
        );

        // service
        $container->set(
            InternalLinkService::class,
            autowire()
        );

        // policy
        $container->get(Authorizer::class)
            ->registerPolicy(
                'internal_links',
                $container->get(InternalLinkPolicy::class)
            );
    }

    public function boot(App $app, ContainerInterface $container): void
    {
        // twig namespace
        $twig = $container->get(Twig::class);
        $twig->getLoader()->addPath(
            __DIR__ . '/views',
            'internal-links'
        );

        // routes
        require __DIR__ . '/routes/admin.php';
    }
};


?>