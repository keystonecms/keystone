<?php

namespace Keystone\Core\User;

use Psr\Http\Message\UploadedFileInterface;
use Keystone\Infrastructure\Paths;

final class AvatarStorage {
    public function __construct(
        private Paths $paths
    ) {}

    public function store(
        UploadedFileInterface $file,
        int $userId
    ): StoredAvatar {
        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Avatar upload failed');
        }

        $ext = pathinfo(
            $file->getClientFilename(),
            PATHINFO_EXTENSION
        );

        $filename = 'avatar_' . $userId . '.' . $ext;

        $relative = '/avatars/' . $filename;
        $absolute = $this->paths->uploads() . $relative;

        if (!is_dir(dirname($absolute))) {
            mkdir(dirname($absolute), 0775, true);
        }

        $file->moveTo($absolute);

        return new StoredAvatar(
            path: $relative,
            mimeType: $file->getClientMediaType(),
            size: $file->getSize(),
        );
    }
}


?>