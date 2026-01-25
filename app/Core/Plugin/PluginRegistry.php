<?php

declare(strict_types=1);

namespace Keystone\Core\Plugin;

use PDO;
use RuntimeException;
use Keystone\Core\Plugin\PluginRegistryInterface;

final class PluginRegistry implements PluginRegistryInterface {

    public function __construct(
        private PDO $db
    ) {}


public function allIndexedByPackage(): array {

    $indexed = [];

    foreach ($this->all() as $plugin) {
        $indexed[$plugin->getPackage()] = $plugin;
    }

    return $indexed;
}

    public function exists(string $slug): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM plugins WHERE slug = ?'
        );
        $stmt->execute([$slug]);

        return (bool) $stmt->fetchColumn();
    }

    public function register(
        string $slug,
        string $version,
        bool $enabled = false
    ): void {
        $stmt = $this->db->prepare(
            'INSERT INTO plugins (slug, version, enabled, installed_at)
             VALUES (?, ?, ?, NOW())'
        );

        $stmt->execute([
            $slug,
            $version,
            (int) $enabled,
        ]);
    }

    public function enable(string $slug): void
    {
        $this->db
            ->prepare(
                'UPDATE plugins SET enabled = 1 WHERE slug = ?'
            )
            ->execute([$slug]);
    }

    public function disable(string $slug): void
    {
        $this->db
            ->prepare(
                'UPDATE plugins SET enabled = 0 WHERE slug = ?'
            )
            ->execute([$slug]);
    }

    public function isEnabled(string $slug): bool
    {
        $stmt = $this->db->prepare(
            'SELECT enabled FROM plugins WHERE slug = ?'
        );
        $stmt->execute([$slug]);

        return (bool) $stmt->fetchColumn();
    }

    public function updateVersion(string $slug, string $version): void
    {
        $stmt = $this->db->prepare(
            'UPDATE plugins
             SET version = ?, updated_at = NOW()
             WHERE slug = ?'
        );

        $stmt->execute([$version, $slug]);
    }

    public function get(string $slug): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM plugins WHERE slug = ?'
        );
        $stmt->execute([$slug]);

        $plugin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$plugin) {
            throw new RuntimeException(
                "Plugin [$slug] not found in registry."
            );
        }

        return $plugin;
    }

    public function all(): array
    {
        return $this->db
            ->query('SELECT * FROM plugins ORDER BY slug')
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allIndexedBySlug(): array
    {
        $rows = $this->all();
        $indexed = [];

        foreach ($rows as $row) {
            $indexed[$row['slug']] = $row;
        }

        return $indexed;
    }
}


?>