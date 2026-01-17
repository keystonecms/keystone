<?php

declare(strict_types=1);

use function DI\autowire;
use Keystone\Core\Plugin\PluginInterface;
use Keystone\Core\Auth\Authorizer;
use Keystone\Plugins\System\Domain\UpdatePolicy;
use Keystone\Plugins\System\Infrastructure\GitHub\GitHubReleaseFetcher;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Views\Twig;
use Keystone\Plugins\System\Infrastructure\Update\ReleaseDownloader;
use Keystone\Plugins\System\Domain\UpdateStatusService;


return new class implements PluginInterface {

    public function getName(): string
    {
        return 'system';
    }

    public function getVersion(): string
    {
        return 'v1.0.0';
    }

    public function getDescription(): string
    {
        return 'System management (updates, maintenance)';
    }

    public function register(ContainerInterface $container): void
    {



$container->set(
    ReleaseDownloader::class,
    fn () => new ReleaseDownloader($_ENV['GITHUB_TOKEN'])
);

        /*
         * Policy
         */
        $container->get(Authorizer::class)
            ->registerPolicy(
                'system',
                $container->get(UpdatePolicy::class)
            );

        /*
         * GitHub Release Fetcher
         * (private repo, token-based)
         */
        $container->set(
            GitHubReleaseFetcher::class,
            function () {
                return new GitHubReleaseFetcher(
                    $_ENV['GITHUB_TOKEN'],
                    $_ENV['GITHUB_REPO']
                );
            }
        );
$container->set(UpdateStatusService::class, function ($c) {
    return new UpdateStatusService(
        $c->get(GitHubReleaseFetcher::class),
        KEYSTONE_VERSION
    );
});


    }

public function boot(App $app, ContainerInterface $container): void
{
    // Twig namespace
    $twig = $container->get(\Slim\Views\Twig::class);
    $twig->getLoader()->addPath(
        __DIR__ . '/views',
        'system'
    );

    // Alleen admin-context → update status beschikbaar maken
    $currentUser = $container->get(\Keystone\Domain\User\CurrentUser::class);

    if ($currentUser->isAuthenticated() && $currentUser->user()->hasRole('admin')) {
        $updateStatus = $container
            ->get(\Keystone\Plugins\System\Domain\UpdateStatusService::class)
            ->check();

        $twig->addGlobal('update', $updateStatus);
    }

    // Routes
    require __DIR__ . '/routes/admin.php';
}

};
