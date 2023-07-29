<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Interceptor;

use Panelop\Core\Interceptor\Builders\InterceptorBuilder;
use Panelop\Core\Interceptor\Factories\InvocationMethodFactory;
use Panelop\Core\Interceptor\Interfaces\InterceptorAfterInterface;
use Panelop\Core\Interceptor\Interfaces\InterceptorAroundInterface;
use Panelop\Core\Interceptor\Interfaces\InterceptorBeforeInterface;
use Panelop\Core\Interceptor\InvocationMethod;
use Panelop\Core\Interceptor\InvocationParameter;
use Panelop\Core\Tests\App;
use Panelop\Core\Tests\Classes\Interceptor\RequestHeaders;
use Panelop\Core\Tests\Classes\Interceptor\RequestHeaders\HeaderCollectionAroundInterceptor;
use Panelop\Core\Tests\Classes\Interceptor\RequestHeaders\LoggerBeforeInterceptor;
use Panelop\Core\Tests\Classes\Interceptor\RequestHeaders\RequestHeader;
use Panelop\Core\Tests\DataProviders\Interceptor\RequestHeaderDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(InterceptorBeforeInterface::class)]
#[CoversClass(InterceptorAroundInterface::class)]
#[CoversClass(InterceptorAfterInterface::class)]
#[CoversClass(InvocationMethodFactory::class)]
#[CoversClass(InvocationMethod::class)]
#[CoversClass(InvocationParameter::class)]
#[CoversClass(InterceptorBuilder::class)]
final class InterceptorComplexTest extends TestCase
{
    #[DataProviderExternal(RequestHeaderDataProvider::class, 'fewAmountDataProvider')]
    public function testRequestMessages(
        array $headers,
    ): void {
        /**
         * @var LoggerBeforeInterceptor           $logger
         * @var HeaderCollectionAroundInterceptor $headerCollection
         * @var RequestHeaders                    $requestHeaders
         */
        $logger = App::$container->get(LoggerBeforeInterceptor::class);
        $logger->log = [];

        $headerCollection = App::$container->get(HeaderCollectionAroundInterceptor::class);
        $headerCollection->collection = [];

        $requestHeaders = App::$container->get(RequestHeaders::class);

        foreach ($headers as $header) {
            $requestHeaders->add($header);

            self::assertSame(
                [
                    'method' => 'add',
                    'parameters' => ['headerString'],
                    'arguments' => [$header],
                ],
                $logger->getLastRecord()
            );
        }

        try {
            $requestHeaders->get('X-Custom-Header');
        } catch (RuntimeException $exception) {
            self::assertEquals("Header value is invalid.", $exception->getMessage());
        }

        self::assertSame(
            [
                'method' => 'get',
                'parameters' => ['headerName'],
                'arguments' => ['X-Custom-Header'],
            ],
            $logger->getLastRecord()
        );

        self::assertNotEmpty($requestHeaders->get('Host'));
        self::assertSame(
            [
                'method' => 'get',
                'parameters' => ['headerName'],
                'arguments' => ['Host'],
            ],
            $logger->getLastRecord()
        );

        self::assertEquals("www.example.com", $requestHeaders->get('Host'));
        self::assertSame(
            [
                'method' => 'get',
                'parameters' => ['headerName'],
                'arguments' => ['Host'],
            ],
            $logger->getLastRecord()
        );

        self::assertEquals("www.example.com", $headerCollection->getValue('Host'));

        self::assertNotEmpty($requestHeaders->get('User-Agent'));
        self::assertSame(
            [
                'method' => 'get',
                'parameters' => ['headerName'],
                'arguments' => ['User-Agent'],
            ],
            $logger->getLastRecord()
        );

        self::assertEquals(
            "Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0",
            $requestHeaders->get('User-Agent')
        );
        self::assertSame(
            [
                'method' => 'get',
                'parameters' => ['headerName'],
                'arguments' => ['User-Agent'],
            ],
            $logger->getLastRecord()
        );

        self::assertEquals(
            "Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0",
            $headerCollection->getValue('User-Agent')
        );

        $header = $headerCollection->get('Accept');
        self::assertInstanceOf(RequestHeader::class, $header);
        self::assertEquals("accept", $header->name);
        self::assertEquals(
            "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8",
            $header->value
        );

        self::assertSameSize($headers, $requestHeaders->flush());
        self::assertSame(
            [
                'method' => 'flush',
                'parameters' => [],
                'arguments' => [],
            ],
            $logger->getLastRecord()
        );

        self::assertCount($headerCollection->size(), $headers);

        self::assertEmpty($requestHeaders->flush());
        self::assertSame(
            [
                'method' => 'flush',
                'parameters' => [],
                'arguments' => [],
            ],
            $logger->getLastRecord()
        );
    }
}
