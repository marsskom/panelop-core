<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Unit\Pipeline;

use Panelop\Core\Pipeline\DefaultProcessor;
use Panelop\Core\Pipeline\Interfaces\PipelineInterface;
use Panelop\Core\Pipeline\Interfaces\ProcessorInterface;
use Panelop\Core\Pipeline\Pipeline;
use Panelop\Core\Tests\App;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function substr;

#[CoversClass(Pipeline::class)]
#[CoversClass(DefaultProcessor::class)]
final class PipelineTest extends TestCase
{
    public function testImmutability(): void
    {
        $pipeline = App::$container->get(PipelineInterface::class);

        self::assertNotSame($pipeline, $pipeline->pipe(static fn (): float => 17.7));
    }

    public function testWithoutPayloadUsage(): void
    {
        $pipeline = App::$container->get(PipelineInterface::class)
            ->pipe(static fn (): float => 17.7);

        self::assertEquals(17.7, $pipeline('some payload'));
    }

    public function testSumPipeline(): void
    {
        $pipeline = App::$container->get(PipelineInterface::class)
            ->pipe(static fn (int $value) => $value + 10);

        self::assertEquals(20, $pipeline(10));

        $pipeline = App::$container->get(PipelineInterface::class)
            ->pipe(static fn (int $value): int => $value + 10)
            ->pipe(static fn (int $value): int => $value + 20)
            ->pipe(static fn (int $value): float => $value + 30.33);

        self::assertEquals(70.33, $pipeline(10));
    }

    public function testConcatenationPipeline(): void
    {
        $pipeline = App::$container->get(PipelineInterface::class)
            ->pipe(static fn (string $value): string => $value . '_')
            ->pipe(static fn (string $value): string => $value . '-')
            ->pipe(static fn (string $value): string => $value . '%');

        self::assertEquals('test_-%', $pipeline('test'));
    }

    public function testOnArrayPayloadPipelineWithComposedPipeline(): void
    {
        $pipeline = (new Pipeline(
            App::$container->get(ProcessorInterface::class),
            static fn (array $payload): float => $payload['first'] * $payload['second'],
        ))->pipe(
            new Pipeline(
                App::$container->get(ProcessorInterface::class),
                static fn (int|float $payload): string => (string)($payload - 1.1),
                static fn (string $payload): string => substr($payload, 0, 3),
            )
        );

        self::assertEquals(
            244,
            $pipeline([
                'first' => 144,
                'second' => 17,
            ])
        );
    }
}
