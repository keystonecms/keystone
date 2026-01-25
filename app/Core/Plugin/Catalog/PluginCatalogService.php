<?php

declare(strict_types=1);

namespace Keystone\Core\Plugin\Catalog;

use RuntimeException;
use Keystone\Infrastructure\Paths;

final class PluginCatalogService {

private const CATALOG_URL ='https://raw.githubusercontent.com/keystonecms/plugin-catalog/main/plugins.json';
    

public function __construct(
     private Paths $paths
    ) {}

public function fetch(): array {

    $cacheFile = $this->paths->cache() . '/plugin-catalog.json';

     /**
     * Try the cache file
     */
    if (file_exists($cacheFile) && filemtime($cacheFile) > time() - 7200) {
        $cached = file_get_contents($cacheFile);

        if ($cached !== false) {
            $data = json_decode($cached, true);

            if (
                is_array($data)
                && isset($data['plugins'])
                && is_array($data['plugins'])
            ) {
                return $data['plugins'];
            }
        }

        // Cache is corrupt → weg ermee
        @unlink($cacheFile);
    }

     /**
     * file opnieuw opvragen
     */
    $json = @file_get_contents(self::CATALOG_URL);

    if ($json === false || trim($json) === '') {
        throw new RuntimeException(
            'Unable to fetch Keystone plugin catalog'
        );
    }

    $data = json_decode($json, true);

    if (
        !is_array($data)
        || !isset($data['plugins'])
        || !is_array($data['plugins'])
    ) {
        throw new RuntimeException(
            'Invalid plugin catalog format'
        );
    }

    /**
     * Cache veilig opslaan (atomair)
     */
    $tmpFile = $cacheFile . '.tmp';
    file_put_contents($tmpFile, $json);
    rename($tmpFile, $cacheFile);

dd($data['plugins']);

    return $data['plugins'];
}

}


?>