<?php

namespace Keystone\Core\Plugin;

use PDO;
use DateTimeImmutable;
use Keystone\Core\Plugin\PluginEntity;


final class PluginRepository implements PluginRepositoryInterface {
    public function __construct(
        private PDO $pdo
    ) {}



public function allIndexedByPackage(): array
{
    $indexed = [];

    foreach ($this->all() as $plugin) {
        $indexed[$plugin->getPackage()] = $plugin;
    }

    return $indexed;
}



public function all(): array
{
    $rows = $this->pdo
        ->query('SELECT * FROM plugins')
        ->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
        fn (array $row) => new PluginEntity(
            name: $row['name'],
            package: $row['package'],
            version: $row['version'],
            enabled: (bool) $row['enabled'],
            loadOrder: (int) $row['load_order']
        ),
        $rows
    );
}


   public function count(): int {
        $stmt = $this->pdo->prepare('SELECT count(id) AS total FROM plugins');
               
        return (int) $stmt->fetchColumn();
    }


    public function find(string $name): ?array {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM plugins WHERE name = :name'
        );
        $stmt->execute(['name' => $name]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function install(PluginDescriptor $plugin): void {
        $now = new DateTimeImmutable();

        $stmt = $this->pdo->prepare(
            'INSERT INTO plugins (name, package, version, enabled, load_order, installed_at, updated_at)
             VALUES (:name, :package, :version, 0, :load_order, :now, :now)'
        );

        $stmt->execute([
            'name'    => $plugin->name,
            'package'  => $plugin->package,
            'version' => $plugin->version,
            'now'     => $now->format('Y-m-d H:i:s'),
            'enabled' => 0,
            'load_order' => $plugin->loadOrder,
        ]);
    }

    public function enable(string $name): void {
        $this->setEnabled($name, true);
    }

    public function disable(string $name): void {
        $this->setEnabled($name, false);
    }

    public function isEnabled(string $name): bool {
        $stmt = $this->pdo->prepare(
            'SELECT enabled FROM plugins WHERE name = :name'
        );
        $stmt->execute(['name' => $name]);

        return (bool) $stmt->fetchColumn();
    }

    private function setEnabled(string $name, bool $enabled): void {
        $stmt = $this->pdo->prepare(
            'UPDATE plugins SET enabled = :enabled, updated_at = NOW()
             WHERE name = :name'
        );

        $stmt->execute([
            'name'    => $name,
            'enabled' => (int) $enabled,
        ]);
    }
}

?>