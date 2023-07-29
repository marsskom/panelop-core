<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Unit\Classes\Interceptor\Around;

use Panelop\Core\Interceptor\Attributes\InterceptorsAround;
use Panelop\Core\Interceptor\DI\InterceptableClass;
use Panelop\Core\Tests\Unit\Classes\Interceptor\ArrayOfMess;

#[InterceptableClass]
final class ArrayOfMessAroundWrapper extends ArrayOfMess
{
    #[InterceptorsAround(ParametersLogInterceptor::class)]
    public function add(mixed $value): static
    {
        return parent::add($value);
    }

    #[InterceptorsAround(ParametersLogInterceptor::class)]
    public function get(): array
    {
        return parent::get();
    }

    #[InterceptorsAround(ParametersLogInterceptor::class)]
    public function clear(): self
    {
        $new = clone $this;
        $new->arrayOfMess = [];

        return $new;
    }
}
