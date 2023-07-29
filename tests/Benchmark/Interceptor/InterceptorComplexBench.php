<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Benchmark\Interceptor;

use Panelop\Core\Tests\App;
use Panelop\Core\Tests\Unit\Classes\Interceptor\RequestHeaders;
use Panelop\Core\Tests\Unit\Classes\Interceptor\RequestHeaders\HeaderCollectionAroundInterceptor;
use Panelop\Core\Tests\Unit\Classes\Interceptor\RequestHeaders\LoggerBeforeInterceptor;
use Panelop\Core\Tests\Unit\Classes\Interceptor\RequestHeaders\RequestHeader;
use Panelop\Core\Tests\Unit\DataProviders\Interceptor\RequestHeaderDataProvider;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\Revs;

final class InterceptorComplexBench
{
    #[Revs(3)]
    #[Iterations(5)]
    public function benchInterceptor(): void
    {
        /**
         * @var LoggerBeforeInterceptor           $logger
         * @var HeaderCollectionAroundInterceptor $headerCollection
         * @var RequestHeaders                    $requestHeaders
         */
        $logger = App::$container->get(LoggerBeforeInterceptor::class);
        $headerCollection = App::$container->get(HeaderCollectionAroundInterceptor::class);
        $requestHeaders = App::$container->get(RequestHeaders::class);

        foreach (RequestHeaderDataProvider::largeAmountDataProvider() as $data) {
            $logger->log = [];
            $headerCollection->collection = [];

            foreach ($data['headers'] as $header) {
                $requestHeaders->add($header);
            }
        }
    }

    #[Revs(3)]
    #[Iterations(5)]
    public function benchOriginal(): void
    {
        $requestHeaders = new RequestHeaders();

        foreach (RequestHeaderDataProvider::largeAmountDataProvider() as $data) {
            $logs = [];
            $collection = [];

            foreach ($data['headers'] as $header) {
                $requestHeaders->add($header);

                $logs[] = [
                    'method' => 'add',
                    'parameters' => ['headerString'],
                    'arguments' => [$header]
                ];

                $parsedValue = http_parse_headers([$header]);
                foreach ($parsedValue as $headerName => $headerValue) {
                    $collection[$headerName] = new RequestHeader($headerName, $headerValue);
                }
            }
        }
    }
}
