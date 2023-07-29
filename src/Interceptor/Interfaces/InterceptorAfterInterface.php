<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Interfaces;

interface InterceptorAfterInterface
{
    public function __invoke(mixed $payload = null): mixed;
}
