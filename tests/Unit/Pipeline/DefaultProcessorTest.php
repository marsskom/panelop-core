<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Unit\Pipeline;

use Panelop\Core\Pipeline\DefaultProcessor;
use Panelop\Core\Pipeline\Interfaces\ProcessorInterface;
use Panelop\Core\Tests\App;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DefaultProcessor::class)]
final class DefaultProcessorTest extends TestCase
{
    public function testCalculation(): void
    {
        $processor = App::$container->get(ProcessorInterface::class);

        self::assertEquals(
            3,
            ($processor)(
                177,
                static fn(int $value): int => $value - 77,
                static fn(int $value): float => $value / 20,
                static fn(float $value): float => $value - 5,
                static fn(float $value): float => $value + 3,
            )
        );
    }
}
