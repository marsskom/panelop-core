<?php

declare(strict_types=1);

use Panelop\Core\Pipeline\DefaultProcessor;
use Panelop\Core\Pipeline\Interfaces\PipelineInterface;
use Panelop\Core\Pipeline\Interfaces\ProcessorInterface;
use Panelop\Core\Pipeline\Pipeline;
use Psr\Container\ContainerInterface;

use function DI\autowire;

return [
    ProcessorInterface::class => autowire(DefaultProcessor::class),
    PipelineInterface::class => static fn (ContainerInterface $container): Pipeline => new Pipeline(
        $container->get(ProcessorInterface::class)
    ),
];
