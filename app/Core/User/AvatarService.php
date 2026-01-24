<?php

namespace Keystone\Core\User;

use Psr\Http\Message\UploadedFileInterface;
use Keystone\Domain\User\CurrentUser;
use Keystone\Domain\User\UserRepositoryInterface;

final class AvatarService {
    public function __construct(
        private AvatarStorage $storage,
        private CurrentUser $currentUser,
        private UserRepositoryInterface $userRepository
    ) {}

    public function upload(
        UploadedFileInterface $file
        ): AvatarDto {

        $user = $this->currentUser->user();

        $stored = $this->storage->store($file, $user->id());

        $this->userRepository->updateAvatar(
            userId: $user->id(),
            path: $stored->path
        );

        return new AvatarDto(
            path: $stored->path
        );
    }
}


?>