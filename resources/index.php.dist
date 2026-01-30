<?php

declare(strict_types=1);

// Project root = één niveau boven public_html
define('KEYSTONE_ROOT', dirname(__DIR__));

// Active release
define('CURRENT_PATH', KEYSTONE_ROOT . '/current');

// Sanity check
if (!is_file(CURRENT_PATH . '/public/index.php')) {
    http_response_code(500);
    echo 'Keystone bootstrap not found';
    exit;
}

// Forward request to active release
require CURRENT_PATH . '/public/index.php';

