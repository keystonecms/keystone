<?php

declare(strict_types=1);

namespace Keystone\Tests\Unit\Core\Plugin;

use PHPUnit\Framework\TestCase;
use Keystone\Core\Plugin\Catalog\PluginCatalogService;
use Keystone\Infrastructure\Paths;
use RuntimeException;

final class PluginCatalogServiceTest extends TestCase {
    private string $basePath;
    private string $cacheDir;

    protected function setUp(): void {
        $this->basePath = sys_get_temp_dir() . '/keystone-plugin-catalog-test';

        $this->cacheDir = $this->basePath . '/cache';

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        @unlink($this->cacheDir . '/plugin-catalog.json');
        @rmdir($this->cacheDir);
        @rmdir($this->basePath);
    }

    public function test_it_returns_plugins_from_cache(): void
    {
        file_put_contents(
            $this->cacheDir . '/plugin-catalog.json',
            json_encode([
                'plugins' => [
                    ['name' => 'Blog'],
                    ['name' => 'Pages'],
                ],
            ])
        );

        // maak cache "vers"
        touch($this->cacheDir . '/plugin-catalog.json', time());

        $service = new PluginCatalogService(
            new Paths($this->basePath)
        );

        $plugins = $service->fetch();

        $this->assertCount(2, $plugins);
        $this->assertSame('Blog', $plugins[0]['name']);
    }

    public function test_it_throws_exception_on_invalid_json(): void
    {
        file_put_contents(
            $this->cacheDir . '/plugin-catalog.json',
            'not-json'
        );

        touch($this->cacheDir . '/plugin-catalog.json', time());

        $service = new PluginCatalogService(
            new Paths($this->basePath)
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid plugin catalog format');

        $service->fetch();
    }

    public function test_it_throws_exception_when_plugins_key_is_missing(): void
    {
        file_put_contents(
            $this->cacheDir . '/plugin-catalog.json',
            json_encode(['invalid' => []])
        );

        touch($this->cacheDir . '/plugin-catalog.json', time());

        $service = new PluginCatalogService(
            new Paths($this->basePath)
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid plugin catalog format');

        $service->fetch();
    }
}

?>