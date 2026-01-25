<?php

declare(strict_types=1);

namespace Keystone\Core\Plugin\Validation;

use Keystone\Core\Plugin\PluginInterface;
use RuntimeException;
use ReflectionClass;

final class PluginEntryValidator {


    public function validate(string $pluginName, string $pluginPath): void {
        $expectedClass = "Keystone\\Plugin\\{$pluginName}\\Plugin";
        $expectedFile  = $pluginPath . '/Plugin.php';

        // Entry file check
        if (!file_exists($expectedFile)) {
            throw new RuntimeException(
                "Plugin entry file not found: {$expectedFile}"
            );
        }

        // Load class (geen composer, geen container)
        require_once $expectedFile;

        // Class existence
        if (!class_exists($expectedClass)) {
            throw new RuntimeException(
                "Plugin entry class {$expectedClass} not found."
            );
        }

        $reflection = new ReflectionClass($expectedClass);

        // Namespace exact match
        if ($reflection->getNamespaceName() !== "Keystone\\Plugin\\{$pluginName}") {
            throw new RuntimeException(
                "Invalid plugin namespace. Expected Keystone\\Plugin\\{$pluginName}"
            );
        }

        // Interface contract
        if (!$reflection->implementsInterface(PluginInterface::class)) {
            throw new RuntimeException(
                "Plugin {$pluginName} must implement PluginInterface."
            );
        }

        // Verplichte methods (hard)
        foreach (['register', 'boot', 'getName', 'getVersion'] as $method) {
            if (!$reflection->hasMethod($method)) {
                throw new RuntimeException(
                    "Plugin {$pluginName} is missing required method {$method}()."
                );
            }
        }
    }
}

?>