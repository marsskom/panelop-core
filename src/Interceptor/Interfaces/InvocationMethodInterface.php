<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Interfaces;

interface InvocationMethodInterface
{
    public function getName(): string;

    /**
     * @return InvocationParameterInterface[]
     */
    public function getParameters(): array;

    public function getParameter(string $name): InvocationParameterInterface;

    public function setParameters(InvocationParameterInterface ...$parameters): InvocationMethodInterface;

    public function setParameter(InvocationParameterInterface $parameter): InvocationMethodInterface;

    public function getArguments(): array;

    public function proceed(mixed ...$arguments): mixed;
}
