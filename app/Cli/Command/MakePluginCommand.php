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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class MakePluginCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('make:plugin')
            ->setDescription('Create a new Keystone plugin skeleton')
            ->addArgument('name', InputArgument::REQUIRED, 'Plugin name (e.g. Auth)');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        $name = ucfirst($input->getArgument('name'));
        $slug = strtolower($name);

        $basePath = dirname(__DIR__, 3) . "/keystone-plugins/{$slug}";

        if (is_dir($basePath)) {
            $io->error("Plugin {$slug} already exists.");
            return Command::FAILURE;
        }

        mkdir($basePath . '/src', 0777, true);
        mkdir($basePath . '/routes', 0777, true);
        mkdir($basePath . '/views', 0777, true);

        // composer.json
        file_put_contents(
            $basePath . '/composer.json',
            json_encode([
                'name' => "keystonecms/plugin-{$slug}",
                'type' => 'keystone-plugin',
                'autoload' => [
                    'psr-4' => [
                        "Keystone\\Plugin\\{$name}\\" => 'src/'
                    ]
                ],
                'extra' => [
                    'keystone' => [
                        'plugin-class' => "Keystone\\Plugin\\{$name}\\Plugin"
                    ]
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        // Plugin.php
        file_put_contents(
            $basePath . '/src/Plugin.php',
            <<<PHP
<?php
declare(strict_types=1);

namespace Keystone\\Plugin\\{$name};

use Keystone\\Core\\Plugin\\PluginInterface;
use Psr\\Container\\ContainerInterface;
use Slim\\App;

final class Plugin implements PluginInterface
{
    public function getName(): string
    {
        return '{$name}';
    }

    public function getDescription(): string
    {
        return '';
    }

    public function getLoadOrder(): int
    {
        return 0;
    }

    public function register(ContainerInterface \$container): void
    {
    }

    public function boot(App \$app, ContainerInterface \$container): void
    {
    }
}
PHP
        );

        $io->success("Plugin {$name} created at keystone-plugins/{$slug}");

        return Command::SUCCESS;
    }
}
?>