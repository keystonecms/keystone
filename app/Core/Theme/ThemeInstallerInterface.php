<?php

declare(strict_types=1);

namespace Keystone\Core\Theme;

use Slim\Psr7\UploadedFile;

interface ThemeInstallerInterface {

    public function install(UploadedFile $archive): void;

    public function uninstall(string $themeName): void;
}

?>