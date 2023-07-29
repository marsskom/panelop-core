<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor;

use Panelop\Core\Helpers\InvocationMethod\ParameterSortHelper;
use Panelop\Core\Interceptor\Exceptions\InvocationParameterNotFoundException;
use Panelop\Core\Interceptor\Interfaces\InvocationMethodInterface;
use Panelop\Core\Interceptor\Interfaces\InvocationParameterInterface;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;

use function array_filter;
use function array_values;
use function get_class;
use function is_array;
use function is_object;

final class InvocationMethod implements InvocationMethodInterface
{
    private array|ReflectionFunction $method;

    /**
     * @var InvocationParameterInterface[]
     */
    private array $parameters;

    private bool $hasBeenCalled = false;

    public function __construct(
        array|ReflectionFunction     $method,
        InvocationParameterInterface ...$parameters
    ) {
        $this->method = $method;
        $this->parameters = $parameters;
    }

    public function getName(): string
    {
        if (is_array($this->method)) {
            return (new ReflectionMethod($this->method[0], $this->method[1]))->getName();
        }

        return $this->method->getName();
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getParameter(string $name): InvocationParameterInterface
    {
        foreach ($this->parameters as $parameter) {
            if ($parameter->getName() === $name) {
                return $parameter;
            }
        }

        throw InvocationParameterNotFoundException::create($name);
    }

    public function setParameters(InvocationParameterInterface ...$parameters): InvocationMethodInterface
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function setParameter(InvocationParameterInterface $parameter): InvocationMethodInterface
    {
        foreach ($this->parameters as $key => $existedParameter) {
            if ($parameter->getName() === $existedParameter->getName()) {
                $this->parameters[$key] = $parameter;

                return $this;
            }
        }

        $this->parameters[] = $parameter;

        return $this;
    }

    public function getArguments(): array
    {
        $activeParameters = array_values(
            array_filter(
                (new ParameterSortHelper())->byPosition(...$this->parameters),
                static fn(InvocationParameterInterface $invocationParameter): bool => $invocationParameter->isActive(),
            )
        );

        $arguments = [];
        foreach ($activeParameters as $parameter) {
            /** @var InvocationParameterInterface $parameter */
            if ($parameter->isVariadic()) {
                $arguments = array_merge($arguments, $parameter->getValue());
            } else {
                $arguments[] = $parameter->getValue();
            }
        }

        return $arguments;
    }

    public function hasBeenCalled(): bool
    {
        return $this->hasBeenCalled;
    }

    /**
     * @param mixed ...$arguments
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function proceed(mixed ...$arguments): mixed
    {
        $this->hasBeenCalled = true;

        if ($this->method instanceof ReflectionFunction) {
            return $this->method->invokeArgs($arguments);
        }

        $reflectionMethod = (new ReflectionMethod($this->method[0], $this->method[1]));
        $result = $reflectionMethod->invokeArgs($this->method[0], $arguments);

        if (
            is_object($result)
            && $reflectionMethod->getDeclaringClass()->getName() === get_class($result)
        ) {
            $this->method[0] = $result;
        }

        return $result;
    }
}
