<?php

namespace Keystone\Plugins\Media\Infrastructure\Persistence;

use PDO;

final class MediaRepository {
    public function __construct(
        private PDO $pdo
    ) {}


public function find(int $id): ?array
{
    $stmt = $this->pdo->prepare(
        'SELECT * FROM media WHERE id = :id'
    );
    $stmt->execute(['id' => $id]);

    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

public function delete(int $id): void
{
    $stmt = $this->pdo->prepare(
        'DELETE FROM media WHERE id = :id'
    );
    $stmt->execute(['id' => $id]);
}

public function detachEverywhere(int $mediaId): void
{
    $stmt = $this->pdo->prepare(
        'DELETE FROM page_media WHERE media_id = :id'
    );
    $stmt->execute(['id' => $mediaId]);
}


    public function add(
        string $filename,
        string $path,
        string $mimeType,
        int $size
    ): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO media (filename, path, mime_type, size)
             VALUES (:filename, :path, :mime, :size)'
        );

        $stmt->execute([
            'filename' => $filename,
            'path' => $path,
            'mime' => $mimeType,
            'size' => $size,
        ]);
    }

    public function all(): array
    {
        return $this->pdo
            ->query('SELECT * FROM media ORDER BY created_at DESC')
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}


?>