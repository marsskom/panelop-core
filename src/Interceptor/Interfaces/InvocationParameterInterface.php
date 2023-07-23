<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Interfaces;

interface InvocationParameterInterface
{
    public function getPosition(): int;

    public function getName(): string;

    public function getValue(): mixed;
}
