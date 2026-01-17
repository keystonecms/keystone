<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Keystone CMS – Front Controller
|--------------------------------------------------------------------------
| Alle HTTP-verzoeken komen hier binnen.
| Deze file bevat GEEN business logic.
| Alleen bootstrap + run.
*/

// BASE_PATH = root van de actieve release
define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/bootstrap/app.php';

if (!file_exists(BASE_PATH . '/installed.lock')) {
    (require BASE_PATH . '/app/Http/Routes/install.php');
}

$app->run();
