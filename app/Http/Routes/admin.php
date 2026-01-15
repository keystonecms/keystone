<?php

use Keystone\Http\Controllers\Admin\PluginController;

$app->get('/admin/plugins', [PluginController::class, 'index']
);


?>
