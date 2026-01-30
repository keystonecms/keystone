<?php

declare(strict_types=1);

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

namespace Keystone\Core\Update;

use RuntimeException;
use ZipArchive;
use Keystone\Core\Update\Signature\SignatureVerifier;
use Keystone\Infrastructure\Paths;

final class UpdaterService {

    public function __construct(
        private readonly SignatureVerifier $signatureVerifier,
        private readonly Paths $paths
        ) {}



public function activate(
    string $zipPath,
    string $version,
    string $projectRoot
): void {

    $this->signatureVerifier->verify(
        $zipPath,
        $zipPath . '.sig'
    );

    // 1. Dry-run eerst
    $result = $this->dryRun($zipPath);

    if (!$result->isOk()) {
        throw new RuntimeException('Preflight failed');
    }

    // 2. Extract opnieuw (schone temp)
    $tmpDir = sys_get_temp_dir() . '/keystone_release_' . uniqid();
    mkdir($tmpDir);

    $zip = new ZipArchive();
    $zip->open($zipPath);
    $zip->extractTo($tmpDir);
    $zip->close();

    // 3. Activate
    (new ReleaseActivator())->activate(
        $version,
        $tmpDir,
        $projectRoot
    );
}


    public function dryRun(string $zipPath): PreflightResult {

        $this->signatureVerifier->verify(
            $zipPath,
            $zipPath . '.sig'
        );

        $result = new PreflightResult();

        // 1. PHP version
        $result->add(
            'php_version',
            version_compare(PHP_VERSION, '8.2', '>='),
            'PHP version check'
        );

        // 2. Extract ZIP to temp dir in website path
        if (!is_dir($this->paths->temp())) {
            mkdir($this->paths->temp());
            }

        $tmpDir = $this->paths->temp() . '/keystone_update_' . uniqid();
        mkdir($tmpDir);
        
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new RuntimeException('Unable to open ZIP');
        }
        $zip->extractTo($tmpDir);
        $zip->close();

        // 3. Manifest
        $manifestFile = $tmpDir . '/manifest.json';
        if (!file_exists($manifestFile)) {
            $result->add('manifest', false, 'manifest.json missing');
            return $result;
        }

        $manifestData = json_decode(
            file_get_contents($manifestFile),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $manifest = ReleaseManifest::fromArray($manifestData);

        // 4. Required files
        foreach ($manifest->requiredFiles as $file) {
            $ok = file_exists($tmpDir . '/' . $file);
            $result->add("file:$file", $ok, $file);
        }

        // 5. Required directories
        foreach ($manifest->requiredDirectories as $dir) {
            $ok = is_dir($tmpDir . '/' . $dir);
            $result->add("dir:$dir", $ok, $dir);
        }
        return $result;
    }
}

?>