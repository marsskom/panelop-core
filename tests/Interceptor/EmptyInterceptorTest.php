<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Interceptor;

use Panelop\Core\Interceptor\Builders\InterceptorBuilder;
use Panelop\Core\Interceptor\Factories\InvocationMethodFactory;
use Panelop\Core\Interceptor\InvocationMethod;
use Panelop\Core\Interceptor\InvocationParameter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvocationMethodFactory::class)]
#[CoversClass(InvocationMethod::class)]
#[CoversClass(InvocationParameter::class)]
#[CoversClass(InterceptorBuilder::class)]
final class EmptyInterceptorTest extends TestCase
{
    public static function dataProvider(): iterable
    {
        yield "Two Parameters Function" => [
            'callable' => static fn (mixed $first, mixed $second): array => func_get_args(),
            'hasVariadic' => false,
        ];

        yield "Two Parameters And Variadic Function" => [
            'callable' => static fn (mixed $first, mixed $second, mixed ...$last): array => func_get_args(),
            'hasVariadic' => true,
        ];

        yield "Variadic Function" => [
            'callable' => static fn (mixed ...$variadic): array => func_get_args(),
            'hasVariadic' => true,
        ];
    }

    #[DataProvider('dataProvider')]
    public function testEmptyInterceptors(
        callable $callable,
        bool     $hasVariadic,
    ): void {
        self::assertSame(['Hello', 'world!'], $callable('Hello', 'world!'));
        self::assertSame([1, 2], $callable(1, 2));

        if ($hasVariadic) {
            self::assertSame([1, 2, 3, 4, 5], $callable(1, 2, 3, 4, 5));
        }

        $interceptor = (new InterceptorBuilder())
            ->on((new InvocationMethodFactory())->create($callable))
            ->build();

        self::assertSame(['Hello', 'world!'], $interceptor('Hello', 'world!'));
        self::assertSame([1, 2], $interceptor(1, 2));

        if ($hasVariadic) {
            self::assertSame([1, 2, 3, 4, 5], $interceptor(1, 2, 3, 4, 5));
        }
    }
}
