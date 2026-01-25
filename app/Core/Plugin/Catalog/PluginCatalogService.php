<?php

declare(strict_types=1);

namespace Keystone\Core\Plugin\Catalog;

use RuntimeException;
use Keystone\Infrastructure\Paths;

final class PluginCatalogService {

private const CATALOG_URL ='https://raw.githubusercontent.com/keystonecms/plugin-catalog/refs/heads/main/plugins.json?token=GHSAT0AAAAAADS57SDMPU32V727XUR66LYQ2LVZDXQ';
    

public function __construct(
     private Paths $paths
    ) {}

    public function fetch(): array {


    $cacheFile = $this->paths->cache() . '/plugin-catalog.json';

    if (file_exists($cacheFile) && filemtime($cacheFile) > time() - 7200) {
        return json_decode(file_get_contents($cacheFile), true)['plugins'];
    }

       
        $json = @file_get_contents(self::CATALOG_URL);

        file_put_contents($cacheFile, $json);

        if ($json === false) {
            throw new RuntimeException(
                'Unable to fetch Keystone plugin catalog'
            );
        }

        $data = json_decode($json, true);

        if (!is_array($data) || !isset($data['plugins'])) {
            throw new RuntimeException(
                'Invalid plugin catalog format'
            );
        }

        return $data['plugins'];
    }
}


?>