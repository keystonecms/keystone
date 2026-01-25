<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

// Dit is de ENIGE entrypoint voor tests
require __DIR__ . '/../bootstrap/app_test.php';


?>