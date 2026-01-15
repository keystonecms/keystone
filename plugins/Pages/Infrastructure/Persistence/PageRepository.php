<?php

declare(strict_types=1);

namespace Keystone\Plugins\Pages\Infrastructure\Persistence;

use PDO;
use Keystone\Plugins\Pages\Domain\Page;

final class PageRepository
{
    public function __construct(
        private PDO $pdo
    ) {}


    public function detachMedia(int $pageId, int $mediaId): void {
    $stmt = $this->pdo->prepare(
        'DELETE FROM page_media
         WHERE page_id = :page AND media_id = :media'
    );

    $stmt->execute([
        'page' => $pageId,
        'media' => $mediaId
    ]);
}


    public function unsetHomepage(): void {
        $this->pdo->exec(
            'UPDATE pages SET is_homepage = 0 WHERE is_homepage = 1'
        );
    }

    public function setHomepage(int $pageId): void {
        $stmt = $this->pdo->prepare(
            'UPDATE pages SET is_homepage = 1 WHERE id = :id'
        );
        $stmt->execute(['id' => $pageId]);
    }

    public function updateStatus(int $pageId, string $status): void {
        $stmt = $this->pdo->prepare(
            'UPDATE pages SET status = :status WHERE id = :id'
        );
        $stmt->execute([
            'id' => $pageId,
            'status' => $status,
        ]);
    }

    public function updatePageVersion(int $pageId, int $versionId): void {
        $stmt = $this->pdo->prepare(
            'UPDATE pages SET published_version_id = :version WHERE id = :id'
        );
        $stmt->execute([
            'id' => $pageId,
            'version' => $versionId,
        ]);
    }


    public function attachMedia(int $pageId, int $mediaId): void {
    $stmt = $this->pdo->prepare(
        'INSERT IGNORE INTO page_media (page_id, media_id)
         VALUES (:page, :media)'
    );

    $stmt->execute([
        'page' => $pageId,
        'media' => $mediaId
    ]);
}

 public function publish(int $pageId, int $versionId): void {
    $stmt = $this->pdo->prepare(
        'UPDATE pages
         SET status = "published",
             published_version_id = :vid
         WHERE id = :id'
    );

    $stmt->execute([
        'id'  => $pageId,
        'vid' => $versionId,
    ]);
}

public function unpublish(int $pageId): void {
    $stmt = $this->pdo->prepare(
        'UPDATE pages
         SET status = "draft",
             published_version_id = NULL
         WHERE id = :id'
    );

    $stmt->execute(['id' => $pageId]);
}


public function mediaForPage(int $pageId): array
{
    $stmt = $this->pdo->prepare(
        'SELECT m.*
         FROM media m
         JOIN page_media pm ON pm.media_id = m.id
         WHERE pm.page_id = :id'
    );

    $stmt->execute(['id' => $pageId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function all(): array {
        $stmt = $this->pdo->query(
            'SELECT
            p.*,
            (
                SELECT MIN(pp.publish_at)
                FROM page_publications pp
                WHERE pp.page_id = p.id
                  AND pp.executed_at IS NULL
                  AND pp.publish_at > NOW()
            ) AS next_publication
        FROM pages p
        ORDER BY p.title'
        );

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn (array $row) => $this->mapRowToPage($row),
            $rows
        );
    }

    public function findHomepage(): ?Page
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM pages WHERE is_homepage = 1 LIMIT 1'
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRowToPage($row) : null;
    }

    public function findById(int $id): ?Page
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, title, slug, content, status, author_id, template, published_version_id FROM pages WHERE id = :id'
        );

        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRowToPage($row) : null;
    }

    public function findBySlug(string $slug): ?Page {

        $stmt = $this->pdo->prepare(
            'SELECT id, title, slug, content, status, author_id, template, published_version_id
             FROM pages
             WHERE slug = :slug AND status = "published"'
        );

        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRowToPage($row) : null;
    }

    private function mapRowToPage(array $row): Page
    {
        return new Page(
            (int) $row['id'],
            $row['title'],
            $row['slug'],
            $row['content'],
            $row['status'],
            (int) $row['author_id'],
            $row['template'],
            (bool) $row['is_homepage'],
            (int) $row['published_version_id'],
            $row['next_publication']
        );
    }

public function delete(int $id): void {
    $stmt = $this->pdo->prepare(
        'DELETE FROM pages WHERE id = :id'
    );

    $stmt->execute([
        'id' => $id,
    ]);
}


public function create(
    string $title,
    string $slug,
    string $content,
    string $status,
    int $author,
    string $template
): int {
    $stmt = $this->pdo->prepare(
        'INSERT INTO pages (title, slug, content, status, author_id, created_at, template) VALUES (:title, :slug, :content, :status, :author, NOW(), :template)'
    );

    $stmt->execute([
        'title'   => $title,
        'slug'    => $slug,
        'content' => $content,
        'status'  => $status,
        'author' => $author,
        'template' => $template
    ]);
 return (int) $this->pdo->lastInsertId();
    }

public function update(
    int $id,
    string $title,
    string $slug,
    string $content,
    string $template
): int {
    $stmt = $this->pdo->prepare(
        'UPDATE pages
         SET title = :title, slug = :slug, content = :content, template = :template
         WHERE id = :id'
    );

    $stmt->execute([
        'id'      => $id,
        'title'   => $title,
        'slug'    => $slug,
        'content' => $content,
        'template' => $template

        ]);
        return $id;
    }
}


?>