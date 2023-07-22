<?php

declare(strict_types=1);

namespace Panelop\Core\Pipeline;

use Panelop\Core\Pipeline\Interfaces\ProcessorInterface;

use function array_reduce;

final readonly class DefaultProcessor implements ProcessorInterface
{
    public function __invoke(mixed $payload = null, callable ...$callables): mixed
    {
        return array_reduce(
            $callables,
            static fn (mixed $carry, callable $callable): mixed => $callable($carry),
            $payload
        );
    }
}
