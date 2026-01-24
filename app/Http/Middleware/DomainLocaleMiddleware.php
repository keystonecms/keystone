<?php

namespace Keystone\Http\Middleware;

use Keystone\I18n\LocaleContext;
use Keystone\I18n\DomainLocaleResolver;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

final class DomainLocaleMiddleware {

    public function __construct(
        private LocaleContext $localeContext,
        private DomainLocaleResolver $resolver
    ) {}

    public function __invoke(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $host = $request->getUri()->getHost();

        if ($locale = $this->resolver->resolve($host)) {
            $this->localeContext->setLocale($locale);
        }

         return $handler->handle($request);
    }
}


?>