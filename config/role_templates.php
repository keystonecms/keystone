<?php

return [
    'admin' => [
        'label' => 'Administrator',
        'policies' => [
            '*', // speciale wildcard → alles
        ],
    ],

    'editor' => [
        'label' => 'Editor',
        'policies' => [
            'pages.view',
            'pages.create',
            'pages.edit',
            'media.view',
            'media.upload',
        ],
    ],

    'publisher' => [
        'label' => 'Publisher',
        'policies' => [
            'pages.view',
            'pages.create',
            'pages.edit',
            'pages.publish',
            'media.view',
            'media.upload',
        ],
    ],
];

?>