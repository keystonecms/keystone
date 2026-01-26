<?php

namespace Keystone\Infrastructure\Update;


final class VersionReader {
    public function current(): string
    {
        return trim(
            file_get_contents(base_path('VERSION'))
        );
    }
}


?>