<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Classes\Interceptor\Before;

use LogicException;
use Panelop\Core\Interceptor\Interfaces\InterceptorBeforeInterface;
use Panelop\Core\Interceptor\Interfaces\InvocationMethodInterface;

final class ValidatePositiveValueInterceptor implements InterceptorBeforeInterface
{
    public function __invoke(InvocationMethodInterface $invocationMethod): InvocationMethodInterface
    {
        $value = $invocationMethod->getParameter('value')->getValue();
        if ($value < 0) {
            throw new LogicException("Value is negative.");
        }

        return $invocationMethod;
    }
}
