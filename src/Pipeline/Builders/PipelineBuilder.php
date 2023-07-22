<?php

declare(strict_types=1);

namespace Panelop\Core\Pipeline\Builders;

use Panelop\Core\Pipeline\Interfaces\PipelineInterface;
use Panelop\Core\Pipeline\Interfaces\ProcessorInterface;
use Panelop\Core\Pipeline\Pipeline;

final class PipelineBuilder
{
    /**
     * @var callable[]
     */
    private array $callables = [];

    public function add(callable ...$callables): self
    {
        $this->callables = [
            ...$this->callables,
            ...$callables,
        ];

        return $this;
    }

    public function build(ProcessorInterface $processor): PipelineInterface
    {
        return new Pipeline($processor, ...$this->callables);
    }
}
