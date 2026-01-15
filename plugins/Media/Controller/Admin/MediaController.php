<?php


namespace Keystone\Plugins\Media\Controller\Admin;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Keystone\Plugins\Media\Domain\MediaService;
use Psr\Http\Message\UploadedFileInterface;

final class MediaController
{
    public function __construct(
        private MediaService $media,
        private Twig $twig
    ) {}

public function index(
    ServerRequestInterface $request,
    ResponseInterface $response
): ResponseInterface {

    $query  = $request->getQueryParams();
    $picker = isset($query['picker']) && $query['picker'] === '1';

    return $this->twig->render(
        $response,
        '@media/admin/media/index.twig',
        [
            'media'  => $this->media->all(),
            'picker' => $picker,
        ]
    );
}

public function delete(
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $args
): ResponseInterface {

    $this->media->delete((int) $args['id']);

    return $response
        ->withHeader('Location', '/admin/media')
        ->withStatus(302);
}


public function upload(
    ServerRequestInterface $request,
    ResponseInterface $response
): ResponseInterface {

    $files = $request->getUploadedFiles();

    /** @var UploadedFileInterface $file */
    $file = $files['file'] ?? null;

    if ($file) {
        $this->media->upload($file);
    }

    return $response
        ->withHeader('Location', '/admin/media')
        ->withStatus(302);
}

}


?>