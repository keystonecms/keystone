<?php

use function DI\autowire;
use Keystone\Core\Plugin\PluginInterface;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Views\Twig;

use Keystone\Core\Auth\Authorizer;
use Keystone\Plugins\Seo\Domain\{
    SeoRepositoryInterface,
    SeoSubject,
    SeoMetadata,
    SeoService,
    SeoPolicy
};
use Keystone\Plugins\Seo\Twig\SeoExtension;
use Keystone\Plugins\Seo\Infrastructure\Persistence\PdoSeoRepository;


return new class implements PluginInterface {

    public function getName(): string {
        return 'seo';
    }

    public function getVersion(): string {
        return 'v1.0.0';
    }

    public function getDescription(): string {
        return 'SEO metadata management';
    }

    public function register(ContainerInterface $container): void
    {
        // repository
        $container->set(
            SeoRepositoryInterface::class,
            autowire(PdoSeoRepository::class)
        );

        // service
        $container->set(
            SeoService::class,
            autowire()
        );

        // policy
        $container->get(Authorizer::class)
            ->registerPolicy(
                'seo',
                $container->get(SeoPolicy::class)
            );
    }

    public function boot(App $app, ContainerInterface $container): void
    {
        // twig namespace
        $twig = $container->get(Twig::class);
        $twig->getLoader()->addPath(
            __DIR__ . '/views',
            'seo'
        );

          // SEO twig extension
    $twig->addExtension(
        $container->get(SeoExtension::class)
    );

        // routes
        require __DIR__ . '/routes/admin.php';
    }
};


?>