<?php

declare(strict_types=1);

namespace Panelop\Core\Pipeline;

use Panelop\Core\Pipeline\Interfaces\PipelineInterface;
use Panelop\Core\Pipeline\Interfaces\ProcessorInterface;

final readonly class ConditionalProcessor implements ProcessorInterface
{
    public function __construct(
        private PipelineInterface $condition,
    ) {
    }

    public function __invoke(mixed $payload = null, callable ...$callables): mixed
    {
        foreach ($callables as $callable) {
            $payload = $callable($payload);

            if (false === ($this->condition)($payload)) {
                return $payload;
            }
        }

        return $payload;
    }
}
