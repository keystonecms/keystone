<?php

namespace Keystone\Tests;

use PHPUnit\Framework\TestCase;
use Slim\App;

abstract class Tester extends TestCase {
    protected function createApp(): App
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }
}



?>