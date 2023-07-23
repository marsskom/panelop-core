<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION | Attribute::IS_REPEATABLE)]
final class InterceptorAround
{
    public function __construct(private string $className)
    {
    }
}
