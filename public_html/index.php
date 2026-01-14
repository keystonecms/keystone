<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Keystone CMS – Front Controller
|--------------------------------------------------------------------------
| Alle HTTP-verzoeken komen hier binnen.
| Deze file mag GEEN logica bevatten.
| Alleen bootstrap + run.
*/

require __DIR__ . '/../bootstrap/app.php';
$app->run();
?>