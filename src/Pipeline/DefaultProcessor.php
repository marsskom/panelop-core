<?php

declare(strict_types=1);

namespace Panelop\Core\Pipeline;

use Panelop\Core\Pipeline\Interfaces\ProcessorInterface;

use function array_reduce;

final class DefaultProcessor implements ProcessorInterface
{
    public function proceed(mixed $payload = null, callable ...$callables): mixed
    {
        return array_reduce(
            $callables,
            static fn(mixed $carry, callable $callable): mixed => $callable($carry),
            $payload
        );
    }

    public function __invoke(mixed $payload = null, callable ...$callables): mixed
    {
        return $this->proceed($payload, $callables);
    }
}
