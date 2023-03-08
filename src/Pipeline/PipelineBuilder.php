<?php

declare(strict_types=1);

namespace Panelop\Core\Pipeline;

use Panelop\Core\Pipeline\Interfaces\PipelineBuilderInterface;
use Panelop\Core\Pipeline\Interfaces\PipelineInterface;
use Panelop\Core\Pipeline\Interfaces\ProcessorInterface;

final class PipelineBuilder implements PipelineBuilderInterface
{
    /**
     * @var callable[]
     */
    private array $callables = [];

    public function add(callable $callable): PipelineBuilderInterface
    {
        $this->callables[] = $callable;

        return $this;
    }

    public function build(ProcessorInterface $processor = null): PipelineInterface
    {
        return new Pipeline($processor, ...$this->callables);
    }
}
