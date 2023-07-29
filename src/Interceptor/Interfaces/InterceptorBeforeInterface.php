<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Interfaces;

interface InterceptorBeforeInterface
{
    public function __invoke(InvocationMethodInterface $invocationMethod): InvocationMethodInterface;
}
