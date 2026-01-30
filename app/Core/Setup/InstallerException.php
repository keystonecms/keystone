<?php

declare(strict_types=1);

namespace Keystone\Core\Setup;

use RuntimeException;

final class InstallerException extends RuntimeException {
    private array $errors;

    public function __construct(array $errors)
    {
        parent::__construct('Installer error');
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}


?>