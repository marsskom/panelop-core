<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Interfaces;

use Panelop\Core\InvocationInterface;

interface InterceptorInterface extends InvocationInterface
{
    public function __invoke(mixed $payload = null): mixed;
}
