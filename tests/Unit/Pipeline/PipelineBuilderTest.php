<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\Unit\Pipeline;

use Panelop\Core\Pipeline\Builders\PipelineBuilder;
use Panelop\Core\Pipeline\DefaultProcessor;
use Panelop\Core\Pipeline\Interfaces\ProcessorInterface;
use Panelop\Core\Pipeline\Pipeline;
use Panelop\Core\Tests\App;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PipelineBuilder::class)]
#[CoversClass(DefaultProcessor::class)]
#[CoversClass(Pipeline::class)]
final class PipelineBuilderTest extends TestCase
{
    public function testBuilder(): void
    {
        $builder = (new PipelineBuilder())
            ->add(static fn (string $value): string => $value . '-')
            ->add(static fn (string $value): string => $value . 'test');

        $pipeline = $builder->build(App::$container->get(ProcessorInterface::class));

        self::assertEquals('builder-test', $pipeline('builder'));
    }
}
