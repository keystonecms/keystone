<?php

namespace Keystone\Core\Settings;

interface SettingsInterface {
    public function get(string $key, mixed $default = null): mixed;
    public function set(string $key, mixed $value): void;
}


?>