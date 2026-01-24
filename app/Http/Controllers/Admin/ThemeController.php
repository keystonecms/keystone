<?php

declare(strict_types=1);

namespace Keystone\Http\Controllers\Admin;

use Keystone\Core\Theme\ThemeService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Psr\Http\Message\UploadedFileInterface;

final class ThemeController {
    public function __construct(
        private ThemeService $themes,
        private Twig $view
    ) {}

public function index(
        ServerRequestInterface $request,
        ResponseInterface $response
        ): ResponseInterface {

        return $this->view->render($response, 'admin/themes/index.twig', [
                'themes' => $this->themes->listThemes(),
                'active' => $this->themes->active(),
            ]);
}

public function upload(ServerRequestInterface $request): ResponseInterface {


    $file = $request->getUploadedFiles()['theme'] ?? null;

    if (! $file) {
        throw new \RuntimeException('Geen bestand geüpload.');
    }

    $this->themes->install($file);

    return redirect('/admin/themes');
}

public function uninstall(ServerRequestInterface $request): ResponseInterface {


    $data = $request->getParsedBody();

    $this->themes->uninstall($data['theme']);

    return redirect('/admin/themes');
}


public function activate(ServerRequestInterface $request): ResponseInterface {
        $data = $request->getParsedBody();

        $this->themes->activateTheme($data['theme']);

        return redirect('/admin/themes');
    }
}


?>