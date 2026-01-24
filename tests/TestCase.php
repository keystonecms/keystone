<?php

declare(strict_types=1);

namespace Keystone\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Slim\App;
use PDO;

abstract class TestCase extends PHPUnitTestCase {

    protected PDO $db;

    protected function setUp(): void {
        parent::setUp();

        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->loadSchema();
    }

    protected function loadSchema(): void {
        $schema = file_get_contents(__DIR__ . '/schema.sql');
        $this->db->exec($schema);
        }

    protected function createApp(): App {
        return require __DIR__ . '/../bootstrap/app.php';
    }
}


?>