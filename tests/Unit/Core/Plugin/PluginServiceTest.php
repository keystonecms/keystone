<?php

declare(strict_types=1);

namespace Keystone\Tests\Unit\Core\Plugin;

use PHPUnit\Framework\TestCase;
use Keystone\Core\Plugin\PluginService;
use Keystone\Core\Plugin\PluginDiscoveryInterface;
use Keystone\Core\Plugin\PluginRepositoryInterface;
use Keystone\Core\Plugin\PluginSyncServiceInterface;

final class PluginServiceTest extends TestCase {


    public function test_list_plugins_combines_discovery_and_repository(): void {
        // --- Arrange ---

        // Fake discovered plugins (wat uit filesystem / composer komt)
        $discoveredPlugins = [
            (object) [
                'name' => 'Blog',
                'package' => 'keystone/blog',
                'version' => '1.0.0',
                'description' => 'Blog plugin',
            ],
            (object) [
                'name' => 'Pages',
                'package' => 'keystone/pages',
                'version' => '1.2.0',
                'description' => 'Pages plugin',
            ],
        ];

        $discovery = $this->createMock(PluginDiscoveryInterface::class);
        $discovery
            ->expects($this->once())
            ->method('discover')
            ->willReturn($discoveredPlugins);

        // Fake DB entity voor Blog (geïnstalleerd + enabled)
        $blogEntity = new class {
            public function getPackage(): string {
                return 'keystone/blog';
            }
            public function isEnabled(): bool {
                return true;
            }
        };

        $repository = $this->createMock(PluginRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('all')
            ->willReturn([$blogEntity]);

        $sync = $this->createMock(PluginSyncServiceInterface::class);
        $sync
            ->expects($this->once())
            ->method('sync')
            ->with($discoveredPlugins);

        $service = new PluginService(
            $discovery,
            $repository,
            $sync
        );

        // --- Act ---
        $result = $service->listPlugins();

        // --- Assert ---
        $this->assertCount(2, $result);

        $this->assertSame([
            'name'        => 'Blog',
            'package'     => 'keystone/blog',
            'version'     => '1.0.0',
            'description' => 'Blog plugin',
            'installed'   => true,
            'enabled'     => true,
        ], $result[0]);

        $this->assertSame([
            'name'        => 'Pages',
            'package'     => 'keystone/pages',
            'version'     => '1.2.0',
            'description' => 'Pages plugin',
            'installed'   => false,
            'enabled'     => false,
        ], $result[1]);
    }

    public function test_enable_delegates_to_repository(): void
    {
        $repository = $this->createMock(PluginRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('enable')
            ->with('keystone/blog');

        $service = new PluginService(
            $this->createStub(PluginDiscoveryInterface::class),
            $repository,
            $this->createStub(PluginSyncServiceInterface::class)
        );

        $service->enable('keystone/blog');
    }

    public function test_disable_delegates_to_repository(): void
    {
        $repository = $this->createMock(PluginRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('disable')
            ->with('keystone/blog');

        $service = new PluginService(
            $this->createStub(PluginDiscoveryInterface::class),
            $repository,
            $this->createStub(PluginSyncServiceInterface::class)
        );

        $service->disable('keystone/blog');
    }
}


?>