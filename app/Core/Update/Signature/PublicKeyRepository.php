<?php

declare(strict_types=1);

namespace Keystone\Core\Update\Signature;

use Keystone\Infrastructure\Paths;

final class PublicKeyRepository {

public function __construct(
    private readonly Paths $paths
) {}

    public function get(): string
    {
        return file_get_contents(
            $this->paths->base() . '/resources/keys/keystone-cms-update.pub'
        );
    }
}


?>