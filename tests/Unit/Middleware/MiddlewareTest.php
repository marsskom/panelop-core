<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Unit\Middleware;

use PHPUnit\Framework\TestCase;

final class MiddlewareTest extends TestCase
{
    public function testSumMiddlewares(): void
    {
        $this->markTestSkipped("I'm not sure");
        //$sumAnonymousClassFactory = static fn(int|float $value) => new class($value) implements MiddlewareInterface {
        //    public function __construct(
        //        private int|float $value
        //    ) {
        //    }
        //
        //    public function __invoke(mixed $input, callable $next): mixed
        //    {
        //        return $this->value + $next();
        //    }
        //};
        //
        //$middlewareFactory = new MiddlewareFactory();
        //
        //$pipeline = $middlewareFactory->fromArray([
        //    $sumAnonymousClassFactory(1),
        //    $sumAnonymousClassFactory(4),
        //    $sumAnonymousClassFactory(21),
        //]);
        //self::assertEquals(26, $pipeline());
        //
        //$pipeline = $middlewareFactory->fromArray([
        //    $sumAnonymousClassFactory(1.1),
        //    $sumAnonymousClassFactory(4.7),
        //    $sumAnonymousClassFactory(21.8),
        //]);
        //self::assertEquals(27.6, $pipeline());
    }
}
