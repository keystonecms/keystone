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
use Keystone\Infrastructure\Urls;
use Keystone\Infrastructure\Paths;


final class UpdateSource {

    public function __construct(
        private readonly Urls $urls,
        private readonly Paths $paths
    ) {}

    public function downloadLatest(): string {

        $meta = json_decode(
            file_get_contents($this->urls->updateLatest()),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $zipUrl = $meta['zip'] ?? null;
        $sigUrl = $meta['signature'] ?? null;

        if (!$zipUrl || !$sigUrl) {
            throw new RuntimeException('Invalid update metadata');
        }

        if (!is_dir($this->paths->temp())) {
            mkdir($this->paths->temp());
            }

        $tmpDir = $this->paths->temp() . '/keystone_update';
        mkdir($tmpDir);

        $zipPath = $tmpDir . '/' . basename($zipUrl);
        $sigPath = $zipPath . '.sig';

dd($zipUrl);

        file_put_contents($zipPath, file_get_contents($zipUrl));
        file_put_contents($sigPath, file_get_contents($sigUrl));

        return $zipPath;
    }

public function latestVersion(): string {

        $json = file_get_contents(
             $this->urls->updateLatest()
        );

        return json_decode($json, true)['version'];
    }
}

?>