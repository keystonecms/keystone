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

declare(strict_types=1);

namespace Keystone\Cli\Command;

use Keystone\Core\Plugin\PluginInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class PluginDoctorCommand extends Command {


    protected function configure(): void {
        $this
            ->setName('plugin:doctor')
            ->setDescription('Validate Keystone plugins')
            ->addArgument(
                'slug',
                InputArgument::OPTIONAL,
                'Optional plugin slug (e.g. auth)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io   = new SymfonyStyle($input, $output);
        $slug = $input->getArgument('slug');

        $pluginRoot = dirname(__DIR__, 4) . '/keystone-plugins';

        if (!is_dir($pluginRoot)) {
            $io->error('keystone-plugins directory not found.');
            return Command::FAILURE;
        }

        $plugins = $slug
            ? [$pluginRoot . '/' . $slug]
            : glob($pluginRoot . '/*', GLOB_ONLYDIR);

        $errors = 0;

        foreach ($plugins as $path) {
            $errors += $this->checkPlugin($io, $path);
        }

        if ($errors > 0) {
            $io->error("Doctor found {$errors} problem(s).");
            return Command::FAILURE;
        }

        $io->success('All plugins look healthy.');
        return Command::SUCCESS;
    }

    private function checkPlugin(SymfonyStyle $io, string $path): int
    {
        $slug = basename($path);
        $io->section("Plugin: {$slug}");

        $errors = 0;
        $composerFile = $path . '/composer.json';

        if (!file_exists($composerFile)) {
            $io->error('Missing composer.json');
            return 1;
        }

        $composer = json_decode(file_get_contents($composerFile), true);

        if (($composer['type'] ?? null) !== 'keystone-plugin') {
            $io->error('composer.json type must be "keystone-plugin"');
            $errors++;
        }

        $pluginClass = $composer['extra']['keystone']['plugin-class'] ?? null;

        if (!$pluginClass) {
            $io->error('Missing extra.keystone.plugin-class');
            return ++$errors;
        }

        $io->text("Plugin class: {$pluginClass}");

        // PSR-4 validation
        $psr4 = $composer['autoload']['psr-4'] ?? [];

        if (count($psr4) !== 1) {
            $io->warning('Expected exactly one PSR-4 namespace');
        }

        [$namespace, $srcDir] = [key($psr4), current($psr4)];
        $expectedFile = $path . '/' . $srcDir . '/Plugin.php';

        if (!file_exists($expectedFile)) {
            $io->error("Expected entry file not found: {$expectedFile}");
            $errors++;
        }

        // Autoload check
        if (!class_exists($pluginClass)) {
            $io->error("Class {$pluginClass} is not autoloadable");
            $errors++;
            return $errors;
        }

        $plugin = new $pluginClass();

        if (!$plugin instanceof PluginInterface) {
            $io->error("{$pluginClass} must implement PluginInterface");
            $errors++;
        }

        // Class name check
        $ref = new \ReflectionClass($pluginClass);
        if ($ref->getShortName() !== 'Plugin') {
            $io->error('Entry class must be named Plugin');
            $errors++;
        }

        // Executable code heuristic
        $contents = file_get_contents($expectedFile);
        if (preg_match('/\b(die|echo|print|var_dump)\b/', $contents)) {
            $io->warning('Executable code detected in Plugin.php');
        }

        if ($errors === 0) {
            $io->success('OK');
        }

        return $errors;
    }
}

?>