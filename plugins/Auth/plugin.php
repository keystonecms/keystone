<?php

declare(strict_types=1);

use function DI\autowire;

use Keystone\Core\Plugin\PluginInterface;
use Keystone\Core\Auth\Authorizer;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Views\Twig;

use Keystone\Plugins\Auth\Domain\Token\TokenRepositoryInterface;
use Keystone\Plugins\Auth\Infrastructure\Persistence\PdoTokenRepository;
use Keystone\Plugins\Auth\Domain\Token\TokenService;
use Keystone\Plugins\Auth\Infrastructure\Mail\MailerInterface;
use Keystone\Plugins\Auth\Infrastructure\Mail\SmtpMailer;
use Keystone\Plugins\Auth\Domain\AuthPolicy;

return new class implements PluginInterface {

    public function getName(): string
    {
        return 'auth';
    }

    public function getVersion(): string
    {
        return 'v1.0.0';
    }

    public function getDescription(): string
    {
        return 'User authentication and account security';
    }

    public function register(ContainerInterface $container): void
    {

$container->set(
    MailerInterface::class,
    function ($container) {
        $settings = $container->get('settings');

        return new SmtpMailer(
            $container->get(\Slim\Views\Twig::class),
            $container->get(\Psr\Log\LoggerInterface::class),
            $settings['smtp'],
            $settings['mail']['from'],
            $settings['app']['base_url']
        );
    }
);


        // Token repository
    $container->set(
        TokenRepositoryInterface::class,
        autowire(PdoTokenRepository::class)
    );

    // Token service
    $container->set(
        TokenService::class,
        autowire()
    );


        // policy
        $container->get(Authorizer::class)
            ->registerPolicy(
                'auth',
                $container->get(AuthPolicy::class)
            );
    }

    public function boot(App $app, ContainerInterface $container): void {

        // twig namespace
        $twig = $container->get(Twig::class);
        $twig->getLoader()->addPath(
            __DIR__ . '/views',
            'auth'
        );

        // routes
        require __DIR__ . '/routes/admin.php';
        require __DIR__ . '/routes/public.php';
    }
};
?>