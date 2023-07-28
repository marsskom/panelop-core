<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Interfaces;

interface InvocationParameterInterface
{
    public function getPosition(): int;

    public function getName(): string;

    public function isVariadic(): bool;

    public function isActive(): bool;

    public function getValue(): mixed;
}
