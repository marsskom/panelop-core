<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Classes\Interceptor;

use Panelop\Core\Interceptor\Attributes\InterceptorsAfter;
use Panelop\Core\Interceptor\Attributes\InterceptorsAround;
use Panelop\Core\Interceptor\Attributes\InterceptorsBefore;
use Panelop\Core\Interceptor\DI\InterceptableClass;
use Panelop\Core\Tests\Classes\Interceptor\RequestHeaders\HeaderCollectionAroundInterceptor;
use Panelop\Core\Tests\Classes\Interceptor\RequestHeaders\HeaderValidateBeforeInterceptor;
use Panelop\Core\Tests\Classes\Interceptor\RequestHeaders\HeaderValueValidateAfterInterceptor;
use Panelop\Core\Tests\Classes\Interceptor\RequestHeaders\LoggerBeforeInterceptor;

use function str_starts_with;

#[InterceptableClass]
final class RequestHeaders
{
    private array $headerStrings = [];

    #[InterceptorsBefore(
        LoggerBeforeInterceptor::class,
        HeaderValidateBeforeInterceptor::class,
    )]
    #[InterceptorsAround(HeaderCollectionAroundInterceptor::class)]
    public function add(string $headerString): void
    {
        $this->headerStrings[] = $headerString;
    }

    #[InterceptorsBefore(LoggerBeforeInterceptor::class)]
    #[InterceptorsAround(HeaderCollectionAroundInterceptor::class)]
    #[InterceptorsAfter(HeaderValueValidateAfterInterceptor::class)]
    public function get(string $headerName): string
    {
        foreach ($this->headerStrings as $headerString) {
            if (str_starts_with($headerString, $headerName)) {
                return $headerString;
            }
        }

        return '';
    }

    #[InterceptorsBefore(LoggerBeforeInterceptor::class)]
    public function flush(): array
    {
        $result = $this->headerStrings;
        $this->headerStrings = [];

        return $result;
    }
}
