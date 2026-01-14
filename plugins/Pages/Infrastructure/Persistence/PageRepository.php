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

    public function all(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, title, slug, content, status, author_id FROM pages ORDER BY id DESC'
        );

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn (array $row) => $this->mapRowToPage($row),
            $rows
        );
    }

    public function findById(int $id): ?Page
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, title, slug, content, status, author_id FROM pages WHERE id = :id'
        );

        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRowToPage($row) : null;
    }

    public function findBySlug(string $slug): ?Page
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, title, slug, content, status, author_id
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
            (int) $row['author_id']
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
    string $author
): void {
    $stmt = $this->pdo->prepare(
        'INSERT INTO pages (title, slug, content, status, authorid, created_at) VALUES (:title, :slug, :content, :status, :author NOW())'
    );

    $stmt->execute([
        'title'   => $title,
        'slug'    => $slug,
        'content' => $content,
        'status'  => $status,
        'author' => $author
    ]);
}

public function update(
    int $id,
    string $title,
    string $slug,
    string $content
): void {
    $stmt = $this->pdo->prepare(
        'UPDATE pages
         SET title = :title, slug = :slug, content = :content
         WHERE id = :id'
    );

    $stmt->execute([
        'id'      => $id,
        'title'   => $title,
        'slug'    => $slug,
        'content' => $content,
        ]);
    }
}


?>