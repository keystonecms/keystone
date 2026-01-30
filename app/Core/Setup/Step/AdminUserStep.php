<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\Step;

use Keystone\Core\Setup\InstallerState;
use Keystone\Core\Setup\InstallerException;


final class AdminUserStep extends AbstractInstallerStep {

    public function getName(): string {
        return 'admin';
    }


    public function getTitle(): string {
    return 'Create Admin User';
    }

    public function getDescription(): string {
        return 'Create the admin user description.';
        }


    public function shouldRun(InstallerState $state): bool {
        return
            $state->adminEmail !== null &&
            $state->adminName !== null;
    }

public function run(InstallerState $state): void {

    $pdo = $this->createPdoFromState($state);

    // 1. User aanmaken
    $stmt = $pdo->prepare(
        'INSERT INTO users (name, email, password_hash, status)
         VALUES (:name, :email, :password_hash, :status )'
    );

    $plainPassword = substr(
    base64_encode(random_bytes(24)),
    0,
    20
    );

    $plainPassword = str_replace('+', 'c', $plainPassword);

    error_log('logger password ' . $plainPassword);

    $stmt->execute([
        'name' => $state->adminName,
        'email' => $state->adminEmail,
        'password_hash' => password_hash(
            $plainPassword,
            PASSWORD_DEFAULT
        ),
        'status' => 'active',
    ]);

    $userId = (int) $pdo->lastInsertId();

    $state->generatedAdminPassword = $plainPassword;

    // 2. Admin role ophalen via NAME (belangrijk)
    $stmt = $pdo->prepare(
        'SELECT id FROM roles WHERE name = :name LIMIT 1'
    );

    $stmt->execute(['name' => 'admin']);

    $roleId = (int) $stmt->fetchColumn();

    if (!$roleId) {
        throw new InstallerException([
            'Admin role not found. Role seeding failed.'
        ]);
    }

    // 3. User ↔ role koppelen
    $stmt = $pdo->prepare(
        'INSERT INTO user_roles (user_id, role_id)
         VALUES (:user_id, :role_id)'
    );

    $stmt->execute([
        'user_id' => $userId,
        'role_id' => $roleId,
    ]);
}

}




?>