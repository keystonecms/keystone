<?php

namespace Keystone\Infrastructure;

final class Paths {

    public function __construct(
        private readonly string $basePath
    )  {}

    public function themes(): string {
        return $this->basePath . '/themes';
    }

   public function uploads(): string {
        return $this->basePath . '/../public_html/uploads';
    }

    public function plugins(): string {
        return $this->basePath . '/plugins';
    }

    public function resources(): string {
        return $this->basePath . '/resources/lang';
    }

    public function cache(): string {
        return $this->basePath . '/var/cache';
    }
}


?>