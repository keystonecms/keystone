<?php

declare(strict_types=1);

namespace Keystone\Plugins\Pages\Domain;

use Keystone\Domain\User\User;
use Keystone\Plugins\Pages\Infrastructure\Persistence\PageRepository;
use Keystone\Plugins\Pages\Domain\Page;

final class PageService {
    public function __construct(
        private PageRepository $pages
    ) {}

    public function all(): array
 {
        return $this->pages->all();
    }

    public function findBySlug(string $slug): ?Page {
        return $this->pages->findBySlug($slug);
    }

   public function findById(int $id): ?Page {
        return $this->pages->findById($id);
    }

    public function create(array $data, User $user): Page {
        // hier later policies / logging / events
        return $this->pages->create($data, $user);
    }

    public function save(array $data): void {
        $title   = trim($data['title'] ?? '');
        $content = $data['content'] ?? '';
        $slug    = trim($data['slug'] ?? '');

        if ($slug === '') {
            $slug = $this->slugify($title);
        } else {
            $slug = $this->slugify($slug);
        }

        if (!empty($data['id'])) {
            $this->pages->update(
                (int) $data['id'],
                $title,
                $slug,
                $content
            );
        } else {
            $this->pages->create(
                $title,
                $slug,
                $content
            );
        }
    }

    /**
     * Maak een nette URL-slug
     */
    private function slugify(string $value): string {
        $value = strtolower($value);
        $value = trim($value);

        // Accenten verwijderen (é → e, ü → u)
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        // Alles behalve letters, cijfers en spaties verwijderen
        $value = preg_replace('/[^a-z0-9\s-]/', '', $value);

        // Spaties en meerdere streepjes → enkel streepje
        $value = preg_replace('/[\s-]+/', '-', $value);

        return trim($value, '-');
        }
}

?>