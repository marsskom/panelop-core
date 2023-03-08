<?php

declare(strict_types=1);

namespace Panelop\Core\Pipeline\Interfaces;

interface PipelineBuilderInterface
{
    public function add(callable $callable): PipelineBuilderInterface;

    public function build(ProcessorInterface $processor = null): PipelineInterface;
}
