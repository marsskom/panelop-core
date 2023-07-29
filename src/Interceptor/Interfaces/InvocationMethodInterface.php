<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Interfaces;

use Panelop\Core\Interceptor\Exceptions\InvocationParameterNotFoundException;

interface InvocationMethodInterface
{
    public function getName(): string;

    /**
     * @return InvocationParameterInterface[]
     */
    public function getParameters(): array;

    /**
     * @throws InvocationParameterNotFoundException
     */
    public function getParameter(string $name): InvocationParameterInterface;

    public function setParameters(InvocationParameterInterface ...$parameters): InvocationMethodInterface;

    public function setParameter(InvocationParameterInterface $parameter): InvocationMethodInterface;

    public function getArguments(): array;

    public function hasBeenCalled(): bool;

    public function proceed(mixed ...$arguments): mixed;
}
