<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor;

use Panelop\Core\Interceptor\Interfaces\InvocationParameterInterface;

final readonly class InvocationParameter implements InvocationParameterInterface
{
    public function __construct(
        private int    $position,
        private string $name,
        private bool   $isVariadic,
        private bool   $isActive,
        private mixed  $value,
    ) {
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isVariadic(): bool
    {
        return $this->isVariadic;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
