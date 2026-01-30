<?php

namespace Keystone\Core\Setup\Step;

use Keystone\Core\Setup\InstallerState;

final class CheckEnvironmentStep extends AbstractInstallerStep {

public function getName(): string {
        return 'environment';
    }

   public function getTitle(): string {
    return 'Environment check';
    }

    public function getDescription(): string {
        return 'Checking all the settings in your environment to determine if Keystone CMS can run safely. ' .
            'We verify your PHP version, required extensions and file permissions.';
        }

    public function run(InstallerState $state): void {
        $errors = [];

        if (version_compare(PHP_VERSION, '8.3', '<')) {
            $errors[] = 'PHP 8.3 or higher is required';
        }

        $requiredExtensions = [
            'pdo',
            'pdo_mysql',
            'mbstring',
            'json',
            'zip',
        ];

        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                $errors[] = "Missing PHP extension: {$ext}";
            }
        }

        $writablePaths = [
            'storage',
            'cache',
        ];

        foreach ($writablePaths as $path) {
            if (!is_writable(BASE_PATH . '/' . $path)) {
                $errors[] = "Directory not writable: {$path}";
            }
        }

        if ($errors !== []) {
            throw new InstallerException($errors);
        }
    }
}

?>