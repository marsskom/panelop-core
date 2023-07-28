<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Classes\Interceptor\After;

use Panelop\Core\Interceptor\Attributes\InterceptorsAfter;
use Panelop\Core\Interceptor\DI\InterceptableClassInterface;
use Panelop\Core\Tests\Classes\Interceptor\ArrayOfMess;

final class ArrayOfMessAfterWrapper extends ArrayOfMess implements InterceptableClassInterface
{
    public function add(mixed $value): static
    {
        return parent::add($value);
    }

    #[InterceptorsAfter(ConvertToCustomerEntityListInterceptor::class)]
    public function get(): array
    {
        return parent::get();
    }
}
