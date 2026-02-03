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

namespace Keystone\Core\Security;

use RuntimeException;
use Psr\Log\LoggerInterface;

final class BotGuard
{
    private ?LoggerInterface $logger;
    private string $context;
    private string $sessionPrefix;

    public function __construct(
        string $context = 'default',
        ?LoggerInterface $logger = null
    ) {
        $this->context        = $context;
        $this->sessionPrefix = 'botguard_' . $context . '_';
        $this->logger        = $logger;
    }

    /* -------------------------------------------------
     | Form rendering
     * ------------------------------------------------- */

    /**
     * Call when rendering the form
     */
    public function markFormRendered(): void {
        $_SESSION[$this->key('form_time')] = time();
        $_SESSION[$this->key('attempts')]  = 0;
    }

    /**
     * Honeypot field HTML helper (optional)
     */
    public function honeypotField(string $name = 'company'): string {
        return sprintf(
            '<input type="text" name="%s" value="" tabindex="-1" autocomplete="off" style="display:none">',
            htmlspecialchars($name, ENT_QUOTES)
        );
    }

    /* -------------------------------------------------
     | Validation
     * ------------------------------------------------- */

    public function validate(array $input): void
    {
        $this->checkHoneypot($input);
        $this->checkTiming();
        $this->checkRateLimit();
    }

    private function checkHoneypot(array $input): void
    {
        if (!empty($input['company'] ?? '')) {

$this->log('warning', 'Honeypot field filled', [
    'field' => 'company',
]);

            throw new RuntimeException('Invalid request');
        }
    }

    private function checkTiming(int $minSeconds = 4): void
    {
        $key = $this->key('form_time');

        if (
            empty($_SESSION[$key]) ||
            (time() - $_SESSION[$key]) < $minSeconds
        ) {


$this->log('warning', 'Form submitted too quickly', [
    'min_seconds' => $minSeconds,
    'delta'       => time() - ($_SESSION[$this->sessionPrefix . 'form_time'] ?? 0),
]);


            throw new RuntimeException('Form submitted too quickly');
        }
    }

    private function checkRateLimit(int $maxAttempts = 5): void
    {
        $key = $this->key('attempts');

        $_SESSION[$key] = ($_SESSION[$key] ?? 0) + 1;

        if ($_SESSION[$key] > $maxAttempts) {

$this->log('warning', 'BotGuard rate limit exceeded', [
    'attempts' => $_SESSION[$this->sessionPrefix . 'attempts'] ?? null,
]);
            throw new RuntimeException('Too many attempts');
        }
    }

    /* -------------------------------------------------
     | Utilities
     * ------------------------------------------------- */

    private function key(string $suffix): string {
        return $this->sessionPrefix . $suffix;
    }

    /**
     * Call after successful submission
     */
    public function reset(): void {
        unset(
            $_SESSION[$this->key('form_time')],
            $_SESSION[$this->key('attempts')]
        );
    }
}

?>