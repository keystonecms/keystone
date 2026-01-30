<?php

declare(strict_types=1);

use Keystone\Http\Controllers\InstallController;

    $app->group('/installer', function ($group) {

        // UI shell
        $group->get('',[InstallController::class , 'index']);

        // AJAX / JSON
        $group->get('/step/{step}', [InstallController::class, 'step']);
        $group->post('/run', [InstallController::class, 'run']);
        $group->post('/commit', [InstallController::class, 'commit']);
});


?>