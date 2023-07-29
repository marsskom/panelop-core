<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Factories;

use JsonException;
use Panelop\Core\Interceptor\Exceptions\InvocationMethodCannotBeCreatedException;
use Panelop\Core\Interceptor\Interfaces\InvocationMethodInterface;
use Panelop\Core\Interceptor\Interfaces\InvocationParameterInterface;
use Panelop\Core\Interceptor\InvocationMethod;
use Panelop\Core\Interceptor\InvocationParameter;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;

use function is_array;
use function is_callable;

final readonly class InvocationMethodFactory
{
    /**
     * @param array|callable $callable
     *
     * @return InvocationMethodInterface
     * @throws JsonException
     * @throws InvocationMethodCannotBeCreatedException
     * @throws ReflectionException
     */
    public function create(array|callable $callable): InvocationMethodInterface
    {
        $reflectionMethod = match (true) {
            is_array($callable) => new ReflectionMethod($callable[0], $callable[1]),
            is_callable($callable) => new ReflectionFunction($callable),
            default => throw InvocationMethodCannotBeCreatedException::create($callable),
        };

        return new InvocationMethod(
            is_array($callable) ? $callable : $reflectionMethod,
            ...$this->createInvocationParameterFromReflection(...$reflectionMethod->getParameters()),
        );
    }

    public function createInvocationParameterFromReflection(ReflectionParameter ...$parameters): array
    {
        return array_map(
            static fn (ReflectionParameter $parameter): InvocationParameter => new InvocationParameter(
                $parameter->getPosition(),
                $parameter->getName(),
                $parameter->isVariadic(),
                false,
                null
            ),
            $parameters
        );
    }

    public function overrideInvocationParameterValue(
        InvocationParameterInterface $parameter,
        bool                         $isActive,
        mixed                        $value
    ): InvocationParameterInterface {
        return new InvocationParameter(
            $parameter->getPosition(),
            $parameter->getName(),
            $parameter->isVariadic(),
            $isActive,
            $value
        );
    }
}
