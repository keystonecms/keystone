<?php

declare(strict_types=1);

namespace Keystone\Core\Theme;

use Psr\Http\Message\UploadedFileInterface;

interface ThemeInstallerInterface {

    public function install(UploadedFileInterface $archive): void;

    public function uninstall(string $themeName): void;
}

?>