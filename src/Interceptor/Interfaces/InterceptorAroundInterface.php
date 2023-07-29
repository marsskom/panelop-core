<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Interfaces;

interface InterceptorAroundInterface
{
    public function __invoke(InvocationAroundResultInterface $invocationAroundResult): InvocationAroundResultInterface;
}
