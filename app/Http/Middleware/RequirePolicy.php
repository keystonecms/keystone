<?php

declare(strict_types=1);

namespace Keystone\Http\Middleware;

use Keystone\Core\Auth\PolicyResolver;
use Keystone\Domain\User\CurrentUser;
use Keystone\Http\Middleware\PolicyMiddleware;
use Psr\Log\LoggerInterface;

final class RequirePolicy
{
    public function __construct(
        private PolicyResolver $policies,
        private CurrentUser $currentUser,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(string $policy): PolicyMiddleware
    {
        return new PolicyMiddleware(
            $this->policies,
            $this->currentUser,
            $this->logger,
            $policy
        );
    }
}
