<?php

use Slim\Views\Twig;
use Slim\Interfaces\RouteParserInterface;
use Twig\TwigFunction;
use Keystone\Twig\LocaleTwigExtension;

use Keystone\Admin\Menu\AdminMenuRegistry;


$themeManager = $container->get(
    \Keystone\Core\Theme\ThemeManagerInterface::class
);

$themeManager->boot();


$twig = $container->get(Twig::class);

$routeParser = $app->getRouteCollector()->getRouteParser();

$twig->getEnvironment()->addFunction(
    new TwigFunction('path', function (string $name, array $params = []) use ($routeParser) {
        return $routeParser->urlFor($name, $params);
    })
);

$twig->getEnvironment()->addGlobal(
    'auth',
    $container->get(\Keystone\Domain\User\CurrentUser::class)
);
$twig->getEnvironment()->addGlobal('url', $_ENV['APP_URL'] ?? 'http://localhost');
$twig->getEnvironment()->addGlobal('sitename', $_ENV['SITENAME'] ?? 'KeyStone');
$twig->getEnvironment()->addGlobal('base_path', $app->getBasePath());
$twig->getEnvironment()->addGlobal('keystone', ['version' => KEYSTONE_VERSION]);
$twig->getEnvironment()->addFunction(new TwigFunction('asset', fn (string $path) => '/plugins/' . ltrim($path, '/')));
$twig->getEnvironment()->addGlobal('admin_menu',$container->get(AdminMenuRegistry::class)->all());
$twig->getEnvironment()->addExtension($container->get(LocaleTwigExtension::class));
$twig->getEnvironment()->addExtension($container->get(\Keystone\Twig\TranslationTwigExtension::class));

?>