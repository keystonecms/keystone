<?php

namespace Keystone\Plugins\Auth\Controller\Admin;

use Keystone\Domain\User\CurrentUser;
use Keystone\Plugins\Auth\Domain\Auth\TwoFactorEnrollmentService;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;



final class TwoFactorEnrollmentController {


    public function __construct(
        private TwoFactorEnrollmentService $service,
        private CurrentUser $currentUser,
        private Twig $view
    ) {}

    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $secret = $this->service->start($this->currentUser->user());

       


        $uri = $this->service->provisioningUri($this->currentUser->user(), $secret);

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrSvg = $writer->writeString($uri);

        $_SESSION['2fa_enroll_secret'] = $secret;



        return $this->view->render(
            new \Slim\Psr7\Response(),
            '@auth/admin/2fa_enroll.twig',
            [
            'qr' => $qrSvg,
            'secret' => $secret, // fallback
            ]
        );
    }

    public function confirm(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        $secret = $_SESSION['2fa_enroll_secret'] ?? null;

        if (! $secret) {
            throw new \RuntimeException('Enrollment expired');
        }

        $this->service->verifyAndEnable(
            $this->currentUser->user(),
            $secret,
            $data['code']
        );

        unset($_SESSION['2fa_enroll_secret']);

        return (new \Slim\Psr7\Response())
            ->withHeader('Location', '/admin/account')
            ->withStatus(302);
    }
}

?>