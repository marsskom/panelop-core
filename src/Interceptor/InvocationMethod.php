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

use function array_map;
use function array_values;

final class InvocationMethod implements InvocationMethodInterface
{
    private array|ReflectionFunction $method;

    /**
     * @var InvocationParameterInterface[]
     */
    private array $parameters;

    public function __construct(
        array|ReflectionFunction     $method,
        InvocationParameterInterface ...$parameters
    ) {
        $this->method = $method;
        $this->parameters = $parameters;
    }

    public function getName(): string
    {
        return $this->method->getName();
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @throws InvocationParameterNotFoundException
     */
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
        return array_map(
            static fn (InvocationParameterInterface $invocationParameter): mixed => $invocationParameter->getValue(),
            array_values((new ParameterSortHelper())->byPosition(...$this->parameters))
        );
    }

    /**
     * @param mixed ...$arguments
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function proceed(mixed ...$arguments): mixed
    {
        if ($this->method instanceof ReflectionFunction) {
            return $this->method->invokeArgs($arguments);
        }

        return (new ReflectionMethod($this->method[0], $this->method[1]))
            ->invokeArgs($this->method[0], $arguments);
    }
}
