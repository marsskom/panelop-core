<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Interfaces;

interface InvocationInterceptorInterface
{
    public function __invoke(mixed ...$arguments): mixed;
}
