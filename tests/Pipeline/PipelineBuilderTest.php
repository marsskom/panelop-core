<?php

declare(strict_types=1);

namespace Pipeline;

use Panelop\Core\Pipeline\DefaultProcessor;
use Panelop\Core\Pipeline\Pipeline;
use Panelop\Core\Pipeline\PipelineBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PipelineBuilder::class)]
#[CoversClass(DefaultProcessor::class)]
#[CoversClass(Pipeline::class)]
class PipelineBuilderTest extends TestCase
{
    public function testBuilder(): void
    {
        $builder = (new PipelineBuilder())
            ->add(static fn(string $value): string => $value . '-')
            ->add(static fn(string $value): string => $value . 'test');

        $pipeline = $builder->build();

        self::assertEquals('builder-test', $pipeline('builder'));
    }
}
