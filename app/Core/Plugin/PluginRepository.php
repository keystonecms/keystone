<?php

namespace Keystone\Core\Plugin;

use PDO;
use DateTimeImmutable;

final class PluginRepository implements PluginRepositoryInterface {
    public function __construct(
        private PDO $pdo
    ) {}

    public function all(): array {
        return $this->pdo
            ->query('SELECT * FROM plugins')
            ->fetchAll(PDO::FETCH_ASSOC);
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
            'INSERT INTO plugins (name, version, enabled, load_order, installed_at, updated_at)
             VALUES (:name, :version, 0, 100, :now, :now)'
        );

        $stmt->execute([
            'name'    => $plugin->name,
            'version' => $plugin->version,
            'now'     => $now->format('Y-m-d H:i:s'),
            'enabled' => 0,
            'load_order' => $loadOrder,
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