<?php

declare(strict_types=1);

namespace Interceptor;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
final class InterceptorBeforeTest extends TestCase
{
    public function testCallable(): void
    {
        $sum = static function (int|float $first, int|float $second): int|float {
            return $first + $second;
        };

        self::assertEquals(5, $sum(2, 3));
        self::assertEquals(5.9, $sum(2.2, 3.7));

        $interceptorFactory = new InterceptorFactory();
        $interceptorList = $interceptorFactory
            ->cover($sum)
            ->before(
                new class implements InterceptorInterface {
                    public function __invoke(SubjectMethodInterface $method): SubjectMethodInterface
                    {
                        $method->first++;

                        return $method;
                    }
                }
            );

        self::assertEquals(6, $interceptorList->proceed(2, 3));
        self::assertEquals(6.9, $interceptorList->proceed(2.2, 3.7));
    }
}
