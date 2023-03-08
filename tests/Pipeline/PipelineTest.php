<?php

declare(strict_types=1);

namespace Pipeline;

use Panelop\Core\Pipeline\DefaultProcessor;
use Panelop\Core\Pipeline\Pipeline;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function substr;

#[CoversClass(Pipeline::class)]
#[CoversClass(DefaultProcessor::class)]
final class PipelineTest extends TestCase
{
    public function testImmutability(): void
    {
        $pipeline = (new Pipeline());

        self::assertNotSame($pipeline, $pipeline->pipe(static fn(): float => 17.7));
    }

    public function testWithoutPayload(): void
    {
        $pipeline = (new Pipeline())
            ->pipe(static fn(): float => 17.7);

        self::assertEquals(17.7, $pipeline('some payload'));
    }

    public function testSumPipeline(): void
    {
        $pipeline = (new Pipeline())
            ->pipe(static fn(int $value) => $value + 10);

        self::assertEquals(20, $pipeline(10));

        $pipeline = (new Pipeline())
            ->pipe(static fn(int $value): int => $value + 10)
            ->pipe(static fn(int $value): int => $value + 20)
            ->pipe(static fn(int $value): int => $value + 30);

        self::assertEquals(70, $pipeline(10));
    }

    public function testConcatenationPipeline(): void
    {
        $pipeline = (new Pipeline())
            ->pipe(static fn(string $value): string => $value . '_')
            ->pipe(static fn(string $value): string => $value . '-')
            ->pipe(static fn(string $value): string => $value . '%');

        self::assertEquals('test_-%', $pipeline('test'));
    }

    public function testOnArrayPayloadPipelineWithComposedPipeline(): void
    {
        $pipeline = (new Pipeline(
            null,
            static fn(array $payload): float => $payload['first'] * $payload['second'],
        ))->pipe(new Pipeline(
            null,
            static fn(int|float $payload): string => (string)($payload - 1.1),
            static fn(string $payload): string => substr($payload, 0, 3),
        ));

        self::assertEquals(
            244,
            $pipeline([
                'first' => 144,
                'second' => 17,
            ])
        );
    }
}
