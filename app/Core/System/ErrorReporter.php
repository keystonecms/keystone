<?php

namespace Keystone\Core\System;

use Throwable;
use Keystone\Core\System\ErrorRepositoryInterface;

final class ErrorReporter {
    
    public function __construct(
        private ErrorRepositoryInterface $errors
    ) {}

    public function report(Throwable $e, array $context = []): void
    {
        $this->errors->create([
            'level'           => 'error',
            'message'         => $e->getMessage(),
            'exception_class'=> $e::class,
            'file'            => $e->getFile(),
            'line'            => $e->getLine(),
            'trace'           => $e->getTraceAsString(),
            'request_uri'     => $context['uri'] ?? null,
            'method'          => $context['method'] ?? null,
            'user_id'         => $context['user_id'] ?? null,
            'plugin'          => $context['plugin'] ?? null,
        ]);
    }
}


?>