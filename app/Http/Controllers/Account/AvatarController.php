<?php

namespace Keystone\Http\Controllers\Account;

use Keystone\Http\Controllers\BaseController;
use Keystone\Core\User\AvatarService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Keystone\Domain\User\CurrentUser;

final class AvatarController extends BaseController {
  

public function __construct(
        private AvatarService $avatars,
        private CurrentUser $currentUser
    ) {}

    public function upload(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
       
     $file = $request->getUploadedFiles()['file'] ?? null;
       
        $user = $this->currentUser->user();

        if (!$file || !$user) {
            return $this->json($response, [
                'status' => 'error',
                'message' => 'Invalid request'
            ], 400);
        }

        $avatar = $this->avatars->upload($file, $user);

        return $this->json($response, [
            'status' => 'ok',
            'avatar' => [
                'path' => $avatar->path(),
            ],
            'media'  => [
                'id'   => $user->id(),
                'name' => $avatar->path(),    
            ]
        ]);
    }
}

?>