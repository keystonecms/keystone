<?php

declare(strict_types=1);

namespace Keystone\Core\Plugin;

use Psr\Container\ContainerInterface;
use Slim\App;

interface PluginInterface
{
    public function register(ContainerInterface $container): void;

    public function boot(
        App $app,
        ContainerInterface $container
    ): void;

    public function getName(): string;

    public function getVersion(): string;

    public function getDescription(): string;

}


?>