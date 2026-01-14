<?php

use Keystone\Plugins\Pages\Controller\Public\PageController;

$app->get('/', PageController::class . ':show');
$app->get('/{slug}', PageController::class . ':show');

?>



