<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Callables;

use Panelop\Core\Helpers\InvocationMethod\ParameterSortHelper;
use Panelop\Core\Interceptor\Factories\InvocationMethodFactory;
use Panelop\Core\Interceptor\Interfaces\InvocationMethodInterface;
use Panelop\Core\Interceptor\Interfaces\InvocationParameterInterface;
use Panelop\Core\InvocationInterface;

use function array_key_exists;
use function array_slice;

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

        foreach ($parameterList as $key => $parameter) {
            /** @var InvocationParameterInterface $parameter */
            $isActive = array_key_exists($key, $arguments);

            if ($parameter->isVariadic()) {
                $overriddenParameters[] = (new InvocationMethodFactory())->overrideInvocationParameterValue(
                    $parameter,
                    $isActive,
                    !array_key_exists($key, $arguments) ? null : array_slice($arguments, $key)
                );

                break;
            }

            $overriddenParameters[] = (new InvocationMethodFactory())->overrideInvocationParameterValue(
                $parameter,
                $isActive,
                $arguments[$key] ?? null
            );
        }

        return $this->invocationMethod->setParameters(...$overriddenParameters);
    }
}
