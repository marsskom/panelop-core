<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Classes\Interceptor\Before;

use Panelop\Core\Interceptor\Attributes\InterceptorsBefore;
use Panelop\Core\Interceptor\DI\InterceptableClass;
use Panelop\Core\Interceptor\DI\InterceptableClassInterface;
use Panelop\Core\Tests\Classes\Interceptor\ArrayOfMess;

/**
 * You can use interceptable class attribute or interface, or both.
 * It depends on DI that you use.
 */
#[InterceptableClass]
final class ArrayOfMessBeforeWrapper extends ArrayOfMess implements InterceptableClassInterface
{
    #[InterceptorsBefore(ValidatePositiveValueInterceptor::class)]
    public function add(mixed $value): static
    {
        return parent::add($value);
    }

    #[InterceptorsBefore(
        ValidateIntTypeInterceptor::class,
        ValidatePositiveValueInterceptor::class
    )]
    public function addOnlyPositiveInt(mixed $value): self
    {
        return parent::add($value);
    }
}
