<?php

/*
 * Keystone CMS
 *
 * @author Constan van Suchtelen van de Haere <constan.vansuchtelenvandehaere@hostingbe.com>
 * @copyright 2026 HostingBE
 * @package   Keystone CMS
 * @author    HostingBE
 * @license   MIT
 * @link      https://keystone-cms.com
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
 * files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy,
 * modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software
 * is furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
 * BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF
 * OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Keystone\Core\Setup\Step;

use Keystone\Core\Setup\InstallerState;
use Keystone\Core\Setup\InstallerException;

final class CheckEnvironmentStep extends AbstractInstallerStep {
    public function getName(): string {
        return 'environment';
    }

    public function getTitle(): string
    {
        return 'Environment check';
    }

    public function getDescription(): string
    {
        return 'Checking all the settings in your environment to determine if Keystone CMS can run safely. ' .
               'We verify your PHP version, required extensions, filesystem permissions and runtime settings.';
    }

    public function run(InstallerState $state): void {
        $errors   = [];
        $warnings = [];

        /* -------------------------------------------------
         | PHP version & runtime
         * ------------------------------------------------- */

        if (version_compare(PHP_VERSION, '8.3', '<')) {
            $errors[] = 'PHP 8.3 or higher is required';
        }

        if (PHP_INT_SIZE !== 8) {
            $errors[] = 'A 64-bit PHP build is required';
        }

        /* -------------------------------------------------
         | Required PHP extensions (runtime!)
         * ------------------------------------------------- */

        $requiredExtensions = [
            'pdo',
            'pdo_mysql',
            'mbstring',
            'json',
            'zip',
            'openssl',
            'curl',
            'fileinfo',
        ];

        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                $errors[] = "Missing PHP extension: {$ext}";
            }
        }

        /* -------------------------------------------------
         | Database drivers
         * ------------------------------------------------- */

        if (!in_array('mysql', \PDO::getAvailableDrivers(), true)) {
            $errors[] = 'PDO MySQL driver is not available';
        }

        /* -------------------------------------------------
         | Filesystem permissions
         * ------------------------------------------------- */

        $writablePaths = [
            BASE_PATH . '/storage',
            BASE_PATH . '/cache',
            BASE_PATH . '/shared',
        ];

        foreach ($writablePaths as $path) {
            if (!is_dir($path) || !is_writable($path)) {
                $errors[] = "Directory not writable: {$path}";
            }
        }

        /* -------------------------------------------------
         | Session directory (CRITICAL)
         * ------------------------------------------------- */

        $sessionPath = ini_get('session.save_path');

        if (!$sessionPath) {
            $sessionPath = sys_get_temp_dir();
        } else {
            $sessionPath = explode(';', $sessionPath)[0];
        }

        if (!is_dir($sessionPath)) {
            $errors[] = "Session directory does not exist: {$sessionPath}";
        } elseif (!is_writable($sessionPath)) {
            $errors[] = "Session directory is not writable: {$sessionPath}";
        } else {
            // Extra safety: real write test
            $testFile = rtrim($sessionPath, '/') . '/.keystone_session_test';
            if (@file_put_contents($testFile, 'test') === false) {
                $errors[] = "Session directory write test failed: {$sessionPath}";
            } else {
                @unlink($testFile);
            }
        }

        /* -------------------------------------------------
         | Symlink support (required for Keystone)
         * ------------------------------------------------- */

        $tmpTarget = sys_get_temp_dir() . '/ks_symlink_target';
        $tmpLink   = sys_get_temp_dir() . '/ks_symlink_link';

        @file_put_contents($tmpTarget, 'test');
        @symlink($tmpTarget, $tmpLink);

        if (!is_link($tmpLink)) {
            $errors[] = 'Symlink support is required but not available';
        }

        @unlink($tmpTarget);
        @unlink($tmpLink);

        /* -------------------------------------------------
         | Web root sanity check (warning only)
         * ------------------------------------------------- */
            $script = realpath($_SERVER['SCRIPT_FILENAME'] ?? '');
            $expectedPublicPath = realpath(BASE_PATH . '/current/public');

            if (
                !$script ||
                !$expectedPublicPath ||
                !str_starts_with($script, $expectedPublicPath)
            ) {
                $warnings[] =
                    'Web root does not appear to point to current/public. ' .
                    'Ensure your public_html proxy correctly forwards to Keystone.';
            }

        /* -------------------------------------------------
         | Opcache (warning)
         * ------------------------------------------------- */

        if (!ini_get('opcache.enable')) {
            $warnings[] = 'Opcache is disabled (recommended for production)';
        }

        /* -------------------------------------------------
         | HTTPS (warning)
         * ------------------------------------------------- */

        $https =
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';

        if (!$https) {
            $warnings[] = 'HTTPS is not detected (recommended for production)';
        }

        /* -------------------------------------------------
         | Finalize
         * ------------------------------------------------- */

        if ($errors !== []) {
            throw new InstallerException($errors);
        }

        // optioneel: warnings opslaan in state
        // $state->warnings = $warn;
        }
}

?>