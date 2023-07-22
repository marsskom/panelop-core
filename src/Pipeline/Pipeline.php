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
        private readonly ProcessorInterface $processor,
        callable                            ...$callables,
    ) {
        $this->callables = $callables;
    }

    public function pipe(callable ...$callables): PipelineInterface
    {
        $new = clone $this;
        $new->callables = [
            ...$this->callables,
            ...$callables,
        ];

        return $new;
    }

    public function __invoke(mixed $payload = null): mixed
    {
        return ($this->processor)($payload, ...$this->callables);
    }
}
