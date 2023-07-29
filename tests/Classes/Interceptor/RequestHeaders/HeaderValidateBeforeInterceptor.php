<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Classes\Interceptor\RequestHeaders;

use InvalidArgumentException;
use Panelop\Core\Interceptor\Interfaces\InterceptorBeforeInterface;
use Panelop\Core\Interceptor\Interfaces\InvocationMethodInterface;

use function http_parse_headers;
use function in_array;
use function sprintf;

final class HeaderValidateBeforeInterceptor implements InterceptorBeforeInterface
{
    private array $validHeaderNames = [
        "host",
        "user-agent",
        "accept",
        "accept-language",
        "accept-encoding",
        "connection",
        "cookie",
        "upgrade-insecure-requests",
        "sec-fetch-dest",
        "sec-fetch-mode",
        "sec-fetch-site",
        "sec-gpc",
        "dnt",
        "if-modified-since",
    ];

    public function __invoke(InvocationMethodInterface $invocationMethod): InvocationMethodInterface
    {
        $headerValue = $invocationMethod->getParameter('headerString')->getValue();
        $parsedValue = http_parse_headers([$headerValue]);
        foreach ($parsedValue as $headerName => $headerValue) {
            if (!in_array($headerName, $this->validHeaderNames, true)) {
                throw new InvalidArgumentException(sprintf("Header '%s' is invalid.", $headerName));
            }
        }

        return $invocationMethod;
    }
}
