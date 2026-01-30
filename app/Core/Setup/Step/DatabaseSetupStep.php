<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\Step;

use PDO;
use PDOException;
use Keystone\Core\Setup\InstallerState;
use Keystone\Core\Setup\Step\InstallerStepInterface;
use Keystone\Core\Setup\InstallerException;

final class DatabaseSetupStep extends AbstractInstallerStep {

    public function getName(): string {
        return 'database';
    }

    public function getTitle(): string {
    return 'Database settings';
    }

    public function getDescription(): string {
        return 'Database settings description';
        }
    

    public function run(InstallerState $state): void {

        if (!$state->dbHost || !$state->dbName || !$state->dbUser) {
            throw new InstallerException(['Database host, name and user are required']);

        }

            try {
            new PDO(
                sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                    $state->dbHost === 'localhost' ? '127.0.0.1' : $state->dbHost,
                    $state->dbPort,
                    $state->dbName
                ),
                $state->dbUser,
                $state->dbPass ?? '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 3,
                ]
            );
        } catch (PDOException $e) {
 
            $code = $e->getCode();

            $errors = match ($code) {
                '1045' => [
                    'Access denied for database user.',
                    'Please check the username and password.'
                ],
                '1049' => [
                    'Database does not exist.',
                    'Please verify the database name.'
                ],
                '2002' => [
                    'Could not connect to database host.',
                    'Please verify the host and port.'
                ],
                default => [
                    'Could not connect to the database.',
                ],
            };

            throw new InstallerException($errors);
        }
            $state->databaseValidated = true;
        }
}

?>

