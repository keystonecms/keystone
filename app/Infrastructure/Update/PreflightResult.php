<?php

declare(strict_types=1);

namespace Keystone\Infrastructure\Update;

final class PreflightResult {
    private array $checks = [];

    public function add(string $check, bool $ok, string $message): void
    {
        $this->checks[] = [
            'check' => $check,
            'ok' => $ok,
            'message' => $message,
        ];
    }

    public function isOk(): bool
    {
        foreach ($this->checks as $check) {
            if ($check['ok'] === false) {
                return false;
            }
        }
        return true;
    }

    public function all(): array
    {
        return $this->checks;
    }
}

?>