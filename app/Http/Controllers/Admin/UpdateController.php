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

namespace Keystone\Http\Controllers\Admin;

use Keystone\Core\Update\UpdateStatusService;
use Keystone\Http\Controllers\BaseController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Keystone\Core\Update\UpdaterService;
use Keystone\Core\Update\UpdateSource;
use Laminas\Diactoros\Response\JsonResponse;
use Slim\Views\Twig;

final class UpdateController extends BaseController {
    
    public function __construct(
        private readonly UpdateStatusService $updates,
        private readonly UpdateSource $source,
        private readonly UpdaterService $updater,
        private readonly Twig $view
         ) {}

    public function index(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        return $this->view->render($response, '@core/admin/system/update.twig', [
            'status' => $this->updates->getStatus(),
        ]);
    }

public function dryRun(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {

    try {
            // 1. Download latest release (zip + sig)
            $zipPath = $this->source->downloadLatest();

            // 2. Run dry-run
            $result = $this->updater->dryRun($zipPath);

    return $this->json($response, [
        'status'  => 'success',
        'message' => $result->isOk() . " " . $result->toArray()
        ]);

        } catch (\Throwable $e) {
            return $this->json($response, [
                'ok'    => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

?>