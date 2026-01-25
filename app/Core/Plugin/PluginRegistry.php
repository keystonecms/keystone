<?php

declare(strict_types=1);

namespace Keystone\Core\Plugin;

use PDO;
use RuntimeException;
use Keystone\Core\Plugin\PluginRegistryInterface;
use Keystone\Core\Plugin\PluginDescriptor;
use DateTimeImmutable;

final class PluginRegistry implements PluginRegistryInterface {

    public function __construct(
        private PDO $db
    ) {}


public function allIndexedByPackage(): array
{
    $indexed = [];

    foreach ($this->all() as $plugin) {
        $indexed[$plugin['package']] = $plugin;
    }

    return $indexed;
}

public function install(PluginDescriptor $plugin): void {
        $now = new DateTimeImmutable();

        $stmt = $this->db->prepare(
            'INSERT INTO plugins (name, slug, package, version, enabled, load_order, installed_at, updated_at)
             VALUES (:name, :slug, :package, :version, 0, :load_order, :now, :now)'
        );

        $stmt->execute([
            'name'    => $plugin->name,
            'slug'     => $plugin->slug,
            'package'  => $plugin->package,
            'version' => $plugin->version,
            'now'     => $now->format('Y-m-d H:i:s'),
            'enabled' => 0,
            'load_order' => $plugin->loadOrder,
        ]);
    }



public function existsByPackage(string $package): bool
{
    $stmt = $this->db->prepare(
        'SELECT 1 FROM plugins WHERE package = :package LIMIT 1'
    );
    $stmt->execute(['package' => $package]);

    return (bool) $stmt->fetchColumn();
}


public function removeByPackage(string $package): void
{
    $stmt = $this->db->prepare(
        'DELETE FROM plugins WHERE package = :package'
    );
    $stmt->execute(['package' => $package]);
}
/**
 * Does a plugin exists by slug
 */
public function exists(string $slug): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM plugins WHERE slug = ?'
        );
        $stmt->execute([$slug]);

        return (bool) $stmt->fetchColumn();
    }
/**
 * register the plugin in the database
 */
public function register(
    string $slug,
    string $package,
    string $name,
    string $version,
    bool $enabled = false
): void {
    $stmt = $this->db->prepare(
        'INSERT INTO plugins 
         (slug, package, name, version, enabled, installed_at)
         VALUES (?, ?, ?, ?, ?, NOW())'
    );

    $stmt->execute([
        $slug,
        $package,
        $name,
        $version,
        (int) $enabled,
    ]);
}

/**
 * enable the plugin in the database and in the CMS
 */
public function enable(string $slug): void {
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

    public function count(): int {
        $stmt = $this->db->prepare('SELECT count(id) AS total FROM plugins');
               
        return (int) $stmt->fetchColumn();
    }

    public function getByPackage(string $package): ?array {
    $stmt = $this->db->prepare(
        'SELECT * FROM plugins WHERE package = :package LIMIT 1'
    );
    $stmt->execute(['package' => $package]);

    return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
}

public function get(string $slug): array {
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