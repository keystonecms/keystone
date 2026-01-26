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

namespace Keystone\Infrastructure\Env;

use Keystone\Core\Setup\Env\EnvWriterInterface;
use Keystone\Core\Setup\SetupConfig;
use RuntimeException;

final class EnvWriter implements EnvWriterInterface {
    public function __construct(
        private SetupConfig $config
    ) {}

    public function write(array $values): void
    {
        $envPath = $this->config->envPath;

        $content = '';
        foreach ($values as $key => $value) {
            $content .= sprintf("%s=%s\n", $key, $this->escape($value));
        }

        $tmp = $envPath . '.tmp';

        if (file_put_contents($tmp, $content, LOCK_EX) === false) {
            throw new RuntimeException('Unable to write .env temp file');
        }

        rename($tmp, $envPath);
    }

    private function escape(string $value): string
    {
        return preg_match('/\s/', $value)
            ? '"' . addslashes($value) . '"'
            : $value;
    }
}

?>