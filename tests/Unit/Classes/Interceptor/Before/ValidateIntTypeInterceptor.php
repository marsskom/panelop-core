<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Unit\Classes\Interceptor\Before;

use LogicException;
use Panelop\Core\Interceptor\Interfaces\InterceptorBeforeInterface;
use Panelop\Core\Interceptor\Interfaces\InvocationMethodInterface;

use function is_int;

final class ValidateIntTypeInterceptor implements InterceptorBeforeInterface
{
    public function __invoke(InvocationMethodInterface $invocationMethod): InvocationMethodInterface
    {
        $value = $invocationMethod->getParameter('value')->getValue();
        if (!is_int($value)) {
            throw new LogicException("Value is not int.");
        }

        return $invocationMethod;
    }
}
