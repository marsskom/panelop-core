<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Unit\Classes;

final class SimpleArrayWrapper
{
    public function __construct(
        private array $intArray = [],
    ) {
    }

    public function add(int $value): self
    {
        $this->intArray[] = $value;

        return $this;
    }

    public function get(): array
    {
        return $this->intArray;
    }
}
