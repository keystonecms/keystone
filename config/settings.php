<?php

declare(strict_types=1);

return [
    'app' => [
        'base_url' => $_ENV['APP_BASE_URL'] ?? 'http://localhost',
        'env'      => $_ENV['APP_ENV'] ?? 'dev',
    ],

    'mail' => [
        'from' => $_ENV['MAIL_FROM'] ?? 'no-reply@localhost',
    ],
    'smtp' => [
    'host' => $_ENV['SMTP_HOST'] ?? 'localhost',
    'port' => (int) ($_ENV['SMTP_PORT'] ?? 587),
    'user' => $_ENV['SMTP_USER'] ?? null,
    'pass' => $_ENV['SMTP_PASS'] ?? null,
    'encryption' => $_ENV['SMTP_ENCRYPTION'] ?? 'tls',
    ],
    'ipinfo' => [
        'token' => $_ENV['IPINFO_TOKEN'] ?? null,

    ],
    'i18n' => [

    'default_locale' => 'en_US',
    'domains' => [
        'keystone-cms.nl'  => 'nl_NL',
        'keystone-cms.com' => 'en_US',
        'de.keystone-cms.com' => 'de_DE',
        ],
    ],
];


?>