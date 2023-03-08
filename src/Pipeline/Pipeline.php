<?php

declare(strict_types=1);

namespace Panelop\Core\Pipeline;

use Panelop\Core\Pipeline\Interfaces\PipelineInterface;
use Panelop\Core\Pipeline\Interfaces\ProcessorInterface;

final class Pipeline implements PipelineInterface
{
    /**
     * @var callable[]
     */
    private array $callables;

    public function __construct(
        private ?ProcessorInterface $processor = null,
        callable                    ...$callables,
    ) {
        $this->processor ??= new DefaultProcessor();
        $this->callables = $callables;
    }

    public function pipe(callable $callable): PipelineInterface
    {
        $new = clone $this;
        $new->callables[] = $callable;

        return $new;
    }

    public function proceed(mixed $payload = null): mixed
    {
        return $this->processor->proceed($payload, ...$this->callables);
    }

    public function __invoke(mixed $payload = null): mixed
    {
        return $this->proceed($payload);
    }
}
