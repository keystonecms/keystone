<?php

use Keystone\Core\Plugin\PluginInterface;
use Psr\Container\ContainerInterface;
use Slim\App;
use function DI\autowire;
use Slim\Views\Twig;

use Keystone\Plugins\Pages\Domain\PageRepositoryInterface;
use Keystone\Plugins\Pages\Domain\PageService;
use Keystone\Plugins\Pages\Infrastructure\Persistence\PageRepository;
use Keystone\Core\Auth\Authorizer;
use Keystone\Plugins\Pages\Domain\PagePolicy;


return new class implements PluginInterface {

    public function getName(): string {
        return 'Pages';
    }

     public function getVersion(): string {
        return 'v1.0.0';
    }

    public function getDescription(): string
    {
        return 'Cores pages app description';
    }



public function register(ContainerInterface $container): void {
    $container->set(
        PageRepositoryInterface::class,
        autowire(PageRepository::class)
    );

    $container->set(
        PageService::class,
        autowire()
    );

   $container->get(Authorizer::class)
        ->registerPolicy(
            'pages',
            new PagePolicy()
        );    
}


    public function boot(App $app, ContainerInterface $container): void {

        $twig = $container->get(Twig::class);

        $twig->getLoader()->addPath(
            __DIR__ . '/views',
            'pages'
        );

        require __DIR__ . '/routes/admin.php';
        require __DIR__ . '/routes/public.php';
    }

    
};

?>