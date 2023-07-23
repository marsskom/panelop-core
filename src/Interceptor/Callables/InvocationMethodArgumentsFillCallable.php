<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Callables;

use Panelop\Core\Helpers\InvocationMethod\ParameterSortHelper;
use Panelop\Core\Interceptor\Factories\InvocationMethodFactory;
use Panelop\Core\Interceptor\Interfaces\InvocationMethodInterface;
use Panelop\Core\InvocationInterface;

final readonly class InvocationMethodArgumentsFillCallable implements InvocationInterface
{
    public function __construct(
        private InvocationMethodInterface $invocationMethod
    ) {
    }

    public function __invoke(array $arguments = []): InvocationMethodInterface
    {
        $parameterList = (new ParameterSortHelper())->byPosition(...$this->invocationMethod->getParameters());
        $overriddenParameters = [];
        foreach ($arguments as $key => $argument) {
            $parameter = $parameterList[$key] ?? null;
            if (null === $parameter) {
                continue;
            }

            $overriddenParameters[] = (new InvocationMethodFactory())->overrideInvocationParameterValue(
                $parameter,
                $argument
            );
        }

        return $this->invocationMethod->setParameters(...$overriddenParameters);
    }
}
