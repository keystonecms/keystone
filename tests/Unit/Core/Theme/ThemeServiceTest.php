<?php

declare(strict_types=1);

namespace Keystone\Tests\Unit\Core\Theme;

use PHPUnit\Framework\TestCase;
use Keystone\Core\Theme\ThemeService;
use Keystone\Core\Theme\ThemeManagerInterface;
use Keystone\Core\Theme\ThemeInstallerInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use Keystone\Core\Theme\Theme;
use Keystone\Core\Theme\ThemeManifest;

final class ThemeServiceTest extends TestCase {

    public function test_install_delegates_to_installer(): void {
        $file = $this->createMock(UploadedFileInterface::class);

        $installer = $this->createMock(ThemeInstallerInterface::class);
        $installer
            ->expects($this->once())
            ->method('install')
            ->with($file);

        $themes = $this->createStub(ThemeManagerInterface::class);

        $service = new ThemeService($themes, $installer);

        $service->install($file);
    }

    public function test_uninstall_throws_exception_when_theme_is_active(): void
    {
        // $activeTheme = (object) ['name' => 'default'];
        
        $activeTheme = $this->getTheme();

        $themes = $this->createMock(ThemeManagerInterface::class);
        $themes
            ->method('getActiveTheme')
            ->willReturn($activeTheme);

        $installer = $this->createMock(ThemeInstallerInterface::class);
        $installer
            ->expects($this->never())
            ->method('uninstall');

        $service = new ThemeService($themes, $installer);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Actieve theme kan niet verwijderd worden.');

        $service->uninstall('default');
    }

    public function test_uninstall_calls_installer_for_non_active_theme(): void {
       
    
        $activeTheme = $this->getTheme();

        $themes = $this->createMock(ThemeManagerInterface::class);
        $themes
            ->method('getActiveTheme')
            ->willReturn($activeTheme);

        $installer = $this->createMock(ThemeInstallerInterface::class);
        $installer
            ->expects($this->once())
            ->method('uninstall')
            ->with('dark');

        $service = new ThemeService($themes, $installer);

        $service->uninstall('dark');
    }

    public function test_list_themes_delegates_to_manager(): void
    {
        $themesList = [
            (object) ['name' => 'default'],
            (object) ['name' => 'dark'],
        ];

        $themes = $this->createMock(ThemeManagerInterface::class);
        $themes
            ->expects($this->once())
            ->method('all')
            ->willReturn($themesList);

        $installer = $this->createStub(ThemeInstallerInterface::class);

        $service = new ThemeService($themes, $installer);

        $this->assertSame($themesList, $service->listThemes());
    }

    public function test_activate_theme_delegates_to_manager(): void
    {
        $themes = $this->createMock(ThemeManagerInterface::class);
        $themes
            ->expects($this->once())
            ->method('activate')
            ->with('dark');

        $installer = $this->createStub(ThemeInstallerInterface::class);

        $service = new ThemeService($themes, $installer);

        $service->activateTheme('dark');
    }

    public function test_active_returns_active_theme_name(): void {

    $activeTheme = $this->getTheme();

    $themes = $this->createMock(ThemeManagerInterface::class);
            $themes
                ->method('getActiveTheme')
                ->willReturn($activeTheme);

        $service = new ThemeService(
            $themes,
            $this->createStub(ThemeInstallerInterface::class)
        );

        $this->assertSame('default', $service->active());

    }

private function GetTheme() {

    return new Theme(
        name: 'default',
        path: '/tmp/themes/default',
        manifest: $this->getManifest()
    );
}

private function getManifest() {
    
    return new ThemeManifest(
        name: 'Default theme',
        version: '1.0.0'
        );
    }


}


?>