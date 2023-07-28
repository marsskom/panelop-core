<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final readonly class InterceptorsBefore
{
    /**
     * @var string[]
     */
    public array $interceptors;

    public function __construct(string ...$interceptors)
    {
        $this->interceptors = $interceptors;
    }
}
