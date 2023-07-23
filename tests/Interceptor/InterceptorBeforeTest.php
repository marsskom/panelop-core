<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Interceptor;

use Panelop\Core\Interceptor\Builders\InterceptorBuilder;
use Panelop\Core\Interceptor\Factories\InvocationMethodFactory;
use Panelop\Core\Interceptor\Interfaces\InterceptorBeforeInterface;
use Panelop\Core\Interceptor\Interfaces\InvocationMethodInterface;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
final class InterceptorBeforeTest extends TestCase
{
    public function testCallable(): void
    {
        $sum = static fn (int|float $first, int|float $second): int|float => $first + $second;

        self::assertEquals(5, $sum(2, 3));
        self::assertEquals(5.9, $sum(2.2, 3.7));

        $interceptor = (new InterceptorBuilder())
            ->before(
                new class () implements InterceptorBeforeInterface {
                    public function __invoke(InvocationMethodInterface $invocationMethod): InvocationMethodInterface
                    {
                        $parameter = $invocationMethod->getParameter('first');
                        $newParameter = (new InvocationMethodFactory())
                            ->overrideInvocationParameterValue($parameter, $parameter->getValue() + 1);

                        $invocationMethod->setParameter($newParameter);

                        return $invocationMethod;
                    }
                }
            )
            ->on((new InvocationMethodFactory())->create($sum))
            ->build();

        self::assertEquals(6, $interceptor(2, 3));
        self::assertEquals(6.9, $interceptor(2.2, 3.7));
    }
}
