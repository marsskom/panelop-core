<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Classes\Interceptor;

abstract class ArrayOfMess
{
    public function __construct(
        protected array $arrayOfMess = [],
    ) {
    }

    protected function add(mixed $value): static
    {
        $new = clone $this;
        $new->arrayOfMess[] = $value;

        return $new;
    }

    public function get(): array
    {
        return $this->arrayOfMess;
    }
}
