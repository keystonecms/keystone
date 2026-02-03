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

namespace Keystone\Infrastructure;

final class Paths {

    public function __construct(
        private readonly string $basePath
    )  {}

    public function base(): string {
        return $this->basePath;
    }

    public function themes(): string {
        return $this->basePath . '/themes';
    }

    public function downloads(): string {
        return $this->basePath . '/downloads';
    }
   public function uploads(): string {
        return $this->basePath . '/../public_html/uploads';
    }

    public function pluginDevRoots(): array {
        return [
            realpath(__DIR__ . '/../../keystone-plugins'),
        ];
    }

    public function plugins(): string {
        return $this->basePath . '/plugins';
    }
    
    public function pluginsbackup(): string {
        return $this->basePath . '/var/plugins';
    }

    public function resources(): string {
        return $this->basePath . '/resources/lang';
    }

    public function cache(): string {
        return $this->basePath . '/cache';
    }
    public function temp(): string {
        return $this->basePath . '/tmp';
    }

}


?>