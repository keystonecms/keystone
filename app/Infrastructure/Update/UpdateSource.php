<?php

namespace Keystone\Infrastructure\Update;

final class UpdateSource {
    public function latestVersion(): string
    {
        $json = file_get_contents(
            'https://updates.keystonecms.dev/latest.json'
        );

        return json_decode($json, true)['version'];
    }
}

?>