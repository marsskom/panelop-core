<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Unit\Classes\Interceptor\RequestHeaders;

use Panelop\Core\Interceptor\Interfaces\InterceptorAfterInterface;
use RuntimeException;

use function is_string;
use function sprintf;

final class HeaderValueValidateAfterInterceptor implements InterceptorAfterInterface
{
    public function __invoke(mixed $payload = null): mixed
    {
        if (!is_string($payload) || empty($payload)) {
            throw new RuntimeException(sprintf("Header value is invalid."));
        }

        return $payload;
    }
}
