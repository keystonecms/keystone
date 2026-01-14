<?php

use Keystone\Core\Plugin\PluginInterface;
use Psr\Container\ContainerInterface;
use Slim\App;

return new class implements PluginInterface {

    public function getName(): string
    {
        return 'HelloWorld';
    }

    public function register(ContainerInterface $container): void {
        // services registreren
    }

    public function boot(
        App $app,
        ContainerInterface $container
    ): void {
        $app->get('/hello', function ($request, $response) {
            $response->getBody()->write('Hello plugin');
            return $response;
        });
    }
};

?>