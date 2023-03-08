<?php

declare(strict_types=1);

namespace Pipeline;

use Panelop\Core\Pipeline\DefaultProcessor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DefaultProcessor::class)]
final class DefaultProcessorTest extends TestCase
{
    public function testCalculation(): void
    {
        $processor = new DefaultProcessor();

        self::assertEquals(
            3,
            $processor->proceed(
                177,
                static fn(int $value): int => $value - 77,
                static fn(int $value): float => $value / 20,
                static fn(float $value): float => $value - 5,
                static fn(float $value): float => $value + 3,
            )
        );
    }
}
