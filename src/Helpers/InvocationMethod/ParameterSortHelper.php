<?php

declare(strict_types=1);

namespace Panelop\Core\Helpers\InvocationMethod;

use Panelop\Core\Helpers\Array\IndexArrayHelper;
use Panelop\Core\Interceptor\Interfaces\InvocationParameterInterface;
use Panelop\Core\Interceptor\InvocationParameter;

final class ParameterSortHelper
{
    public function byPosition(InvocationParameterInterface ...$parameters): array
    {
        return (new IndexArrayHelper())->index(
            $parameters,
            getKeyCallable: static fn (
                InvocationParameter $invocationParameter
            ): int => $invocationParameter->getPosition(),
            kSort: true,
        );
    }
}
