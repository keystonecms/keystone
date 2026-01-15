<?php

use Slim\Views\Twig;


$twig = $container->get(Twig::class);


$twig->getEnvironment()->addGlobal(
    'auth',
    $container->get(\Keystone\Domain\User\CurrentUser::class)
);
$twig->getEnvironment()->addGlobal('base_path', $app->getBasePath());



?>