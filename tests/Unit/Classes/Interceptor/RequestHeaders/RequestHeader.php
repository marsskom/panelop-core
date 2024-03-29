<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Unit\Classes\Interceptor\RequestHeaders;

final readonly class RequestHeader
{
    public function __construct(
        public string $name,
        public string $value,
    ) {
    }
}
