<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Interceptor;

use Panelop\Core\Interceptor\Builders\InterceptorBuilder;
use Panelop\Core\Interceptor\Factories\InvocationMethodFactory;
use Panelop\Core\Interceptor\Interfaces\InterceptorAroundInterface;
use Panelop\Core\Interceptor\Interfaces\InvocationAroundResultInterface;
use Panelop\Core\Interceptor\InvocationAroundResult;
use Panelop\Core\Interceptor\InvocationMethod;
use Panelop\Core\Interceptor\InvocationParameter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function array_values;
use function func_get_args;

#[CoversClass(InterceptorAroundInterface::class)]
#[CoversClass(InvocationMethodFactory::class)]
#[CoversClass(InvocationMethod::class)]
#[CoversClass(InvocationParameter::class)]
#[CoversClass(InterceptorBuilder::class)]
final class InterceptorAroundCallableTest extends TestCase
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
        bool $hasVariadic,
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

    #[DataProvider('dataProvider')]
    public function testCallable(
        callable $callable,
        bool $hasVariadic,
    ): void {
        self::assertSame(['Hello', 'world!'], $callable('Hello', 'world!'));
        self::assertSame([1, 2], $callable(1, 2));

        if ($hasVariadic) {
            self::assertSame([1, 2, 3, 4, 5], $callable(1, 2, 3, 4, 5));
        }

        $interceptor = (new InterceptorBuilder())
            ->around(
                new class () implements InterceptorAroundInterface {
                    public function __invoke(
                        InvocationAroundResultInterface $invocationAroundResult
                    ): InvocationAroundResultInterface {
                        // Doesn't use original callable.
                        $payload = $invocationAroundResult->getPayload();
                        unset($payload[0]);

                        return new InvocationAroundResult(
                            $invocationAroundResult->getInvocationMethod(),
                            array_values($payload)
                        );
                    }
                }
            )
            ->on((new InvocationMethodFactory())->create($callable))
            ->build();

        self::assertSame(['world!'], $interceptor('Hello', 'world!'));
        self::assertSame([2], $interceptor(1, 2));

        if ($hasVariadic) {
            self::assertSame([2, 3, 4, 5], $interceptor(1, 2, 3, 4, 5));
        }


        $interceptor = (new InterceptorBuilder())
            ->around(
                new class () implements InterceptorAroundInterface {
                    public function __invoke(
                        InvocationAroundResultInterface $invocationAroundResult
                    ): InvocationAroundResultInterface {
                        // Uses callable.
                        $payload = $invocationAroundResult->getInvocationMethod()->proceed(
                            ...$invocationAroundResult->getPayload()
                        );

                        $payload[1] = "-- it is the result --";

                        return new InvocationAroundResult(
                            $invocationAroundResult->getInvocationMethod(),
                            $payload
                        );
                    }
                }
            )
            ->on((new InvocationMethodFactory())->create($callable))
            ->build();

        self::assertSame(['Hello', "-- it is the result --"], $interceptor('Hello', 'world!'));
        self::assertSame([1, "-- it is the result --"], $interceptor(1, 2));

        if ($hasVariadic) {
            self::assertSame([1, "-- it is the result --", 3, 4, 5], $interceptor(1, 2, 3, 4, 5));
        }
    }
}
