<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Classes\Interceptor\RequestHeaders;

use Panelop\Core\Interceptor\Interfaces\InterceptorBeforeInterface;
use Panelop\Core\Interceptor\Interfaces\InvocationMethodInterface;
use Panelop\Core\Interceptor\Interfaces\InvocationParameterInterface;

use function array_map;
use function count;

final class LoggerBeforeInterceptor implements InterceptorBeforeInterface
{
    public array $log = [];

    public function __invoke(InvocationMethodInterface $invocationMethod): InvocationMethodInterface
    {
        $this->log[] = [
            'method' => $invocationMethod->getName(),
            'parameters' => array_map(
                static fn(InvocationParameterInterface $invocationParameter): string => $invocationParameter->getName(),
                $invocationMethod->getParameters()
            ),
            'arguments' => $invocationMethod->getArguments(),
        ];

        return $invocationMethod;
    }

    public function getLastRecord(): array
    {
        return $this->log[count($this->log) - 1];
    }
}
