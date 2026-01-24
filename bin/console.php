#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

/**
 * Bootstrap EXACT hetzelfde als HTTP
 * app.php bouwt container + plugins
 */
$app = require BASE_PATH . '/bootstrap/app.php';

/** @var Psr\Container\ContainerInterface $container */
$container = $app->getContainer();

$application = new Application('Keystone Console', '1.0.0');

/**
 * Commands komen uit plugins
 */
$application->add(
    $container->get(
        Keystone\Plugins\ShoppingCart\Console\SeedCartCommand::class
    )
);

$application->run();


?>
