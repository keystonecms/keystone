<?php

namespace Keystone\Plugins\Media;


use Slim\App;
use Slim\Views\Twig;
use Psr\Container\ContainerInterface;
use Keystone\Core\Plugin\PluginInterface;

use Keystone\Plugins\Media\Domain\MediaService;
use Keystone\Plugins\Media\Domain\MediaPolicy;
use Keystone\Plugins\Media\Infrastructure\Persistence\MediaRepository;
use Keystone\Core\Auth\Authorizer;


use function DI\autowire;

return new class implements PluginInterface {

    public function getName(): string {
        return 'Media';
    }

    public function getVersion(): string
    {
        return 'v1.0.0';
    }

    public function getDescription(): string
    {
        return 'Media app description';
    }

    public function register(ContainerInterface $container): void {
        // repository
        $container->set(
            MediaRepository::class,
            autowire(MediaRepository::class)
        );

        // service
        $container->set(
            MediaService::class,
            autowire(MediaService::class)
        );

        // authorization
        $container->get(Authorizer::class)
            ->registerPolicy(
                'media',
                new MediaPolicy()
            );
    }

    public function boot(App $app, ContainerInterface $container): void
    {
        // twig namespace
        $twig = $container->get(Twig::class);
        $twig->getLoader()->addPath(
            __DIR__ . '/views',
            'media'
        );

        // routes
        require __DIR__ . '/routes/admin.php';
    }
};

?>