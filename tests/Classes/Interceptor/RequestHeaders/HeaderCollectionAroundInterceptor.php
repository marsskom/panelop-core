<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Classes\Interceptor\RequestHeaders;

use Panelop\Core\Interceptor\Interfaces\InterceptorAroundInterface;
use Panelop\Core\Interceptor\Interfaces\InvocationAroundResultInterface;
use Panelop\Core\Interceptor\InvocationAroundResult;

use function count;
use function strtolower;

final class HeaderCollectionAroundInterceptor implements InterceptorAroundInterface
{
    /**
     * @var RequestHeader[]
     */
    public array $collection = [];

    public function __invoke(InvocationAroundResultInterface $invocationAroundResult): InvocationAroundResultInterface
    {
        $invocationMethod = $invocationAroundResult->getInvocationMethod();

        if ('get' === $invocationMethod->getName()) {
            $headerName = strtolower(
                $invocationMethod->getParameter('headerName')->getValue()
            );

            $payload = isset($this->collection[$headerName]) ?
                $this->collection[$headerName]->value
                : $invocationMethod->proceed(...$invocationMethod->getArguments());

            return new InvocationAroundResult(
                $invocationMethod,
                $payload
            );
        }

        $this->beforeAdd($invocationAroundResult);
        $invocationMethod->proceed(...$invocationMethod->getArguments());

        return $invocationAroundResult;
    }

    private function beforeAdd(InvocationAroundResultInterface $invocationAroundResult): void
    {
        $headerValue = $invocationAroundResult->getInvocationMethod()->getParameter('headerString')->getValue();
        $parsedValue = http_parse_headers([$headerValue]);
        foreach ($parsedValue as $headerName => $headerValue) {
            $this->collection[$headerName] = new RequestHeader($headerName, $headerValue);
        }
    }

    public function getValue(string $header): string
    {
        return $this->collection[strtolower($header)]?->value ?? '';
    }

    public function get(string $header): RequestHeader
    {
        return $this->collection[strtolower($header)];
    }

    public function size(): int
    {
        return count($this->collection);
    }
}
