<?php


namespace Keystone\Plugins\Media\Domain;

use RuntimeException;
use Keystone\Plugins\Media\Infrastructure\Persistence\MediaRepository;
use Psr\Http\Message\UploadedFileInterface;

final class MediaService {
    public function __construct(
        private MediaRepository $repository
    ) {}




public function delete(int $mediaId): void
{
    $media = $this->repository->find($mediaId);

    if (!$media) {
        throw new RuntimeException('Media not found');
    }

    // unlink from all pages
    $this->repository->detachEverywhere($mediaId);

    // delete file
    $absolutePath = __DIR__ . '/../../../../public' . $media['path'];

    if (file_exists($absolutePath)) {
        unlink($absolutePath);
    }

    // delete db record
    $this->repository->delete($mediaId);
}


public function upload(UploadedFileInterface $file): void {
    if ($file->getError() !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed');
    }

$allowed = ['image/jpeg', 'image/png', 'image/webp'];

if (!in_array($file->getClientMediaType(), $allowed, true)) {
    throw new RuntimeException('Invalid file type');
}

if ($file->getSize() > 5 * 1024 * 1024) {
    throw new RuntimeException('File too large');
}

$targetDir = __DIR__ . '/../../../public_html/uploads';

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}


    $originalName = $file->getClientFilename();
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);

    $filename = uniqid() . '.' . $extension;
    $targetPath = $targetDir . '/' . $filename;

    $file->moveTo($targetPath);

    $this->repository->add(
        $originalName,
        '/uploads/' . $filename,
        $file->getClientMediaType(),
        $file->getSize()
    );
}


    public function all(): array
    {
        return $this->repository->all();
    }
}


?>