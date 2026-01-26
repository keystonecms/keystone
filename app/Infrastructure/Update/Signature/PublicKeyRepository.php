<?php

declare(strict_types=1);

namespace Keystone\Infrastructure\Update\Signature;

final class PublicKeyRepository {

    public function get(): string
    {
        return file_get_contents(
            base_path('resources/keys/keystone-update.pub')
        );
    }
}


?>