<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Unit\Classes\Interceptor\Around;

use Panelop\Core\Interceptor\Interfaces\InterceptorAroundInterface;
use Panelop\Core\Interceptor\Interfaces\InvocationAroundResultInterface;
use Panelop\Core\Interceptor\Interfaces\InvocationParameterInterface;
use Panelop\Core\Interceptor\InvocationAroundResult;

use function array_map;

final class ParametersLogInterceptor implements InterceptorAroundInterface
{
    public array $logs = [];

    public function __invoke(
        InvocationAroundResultInterface $invocationAroundResult
    ): InvocationAroundResultInterface {
        $result = match ($invocationAroundResult->getInvocationMethod()->getName()) {
            'add' => $this->logAddition($invocationAroundResult),
            'get' => $this->logGetting($invocationAroundResult),
            'clear' => $this->logClearing($invocationAroundResult),
        };

        return new InvocationAroundResult($invocationAroundResult->getInvocationMethod(), $result);
    }

    private function logAddition(InvocationAroundResultInterface $invocationAroundResult): mixed
    {
        $this->logs[] = [
            'parameters' => array_map(
                static fn (InvocationParameterInterface $invocationParameter): string => $invocationParameter->getName(),
                $invocationAroundResult->getInvocationMethod()->getParameters()
            ),
            'arguments' => $invocationAroundResult->getInvocationMethod()->getArguments(),
        ];

        return $invocationAroundResult->getInvocationMethod()->proceed(...$invocationAroundResult->getPayload());
    }

    private function logGetting(InvocationAroundResultInterface $invocationAroundResult): mixed
    {
        $result = $invocationAroundResult->getInvocationMethod()->proceed(...$invocationAroundResult->getPayload());

        $this->logs[] = [
            'result' => !empty($result),
        ];

        return $result;
    }

    private function logClearing(InvocationAroundResultInterface $invocationAroundResult): mixed
    {
        $this->logs[] = [
            'clear' => true,
        ];

        return $invocationAroundResult->getInvocationMethod()->proceed(...$invocationAroundResult->getPayload());
    }
}
