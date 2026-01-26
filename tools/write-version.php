<?php

file_put_contents(
    __DIR__ . '/../VERSION',
    json_decode(
        file_get_contents(__DIR__ . '/../composer.json'),
        true
    )['version']
);

?>
