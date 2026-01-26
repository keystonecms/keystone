<?php

namespace Keystone\Infrastructure\Update;

use Keystone\Infrastructure\Paths;

final class VersionReader {

   public function __construct(
    private Paths $paths
   ) {}

    public function current(): string {

        return trim(
            file_get_contents($this->paths->base() . '/VERSION')
        );
    }
}


?>