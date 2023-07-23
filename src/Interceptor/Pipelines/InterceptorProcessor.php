<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Pipelines;

use Panelop\Core\Pipeline\Interfaces\PipelineInterface;
use Panelop\Core\Pipeline\Interfaces\ProcessorInterface;

final readonly class InterceptorProcessor implements ProcessorInterface
{
    public function __construct(
        private ?PipelineInterface $beforeInterceptors = null,
    ) {
    }

    public function __invoke(mixed $payload = null, callable ...$callables): mixed
    {
        return array_reduce(
            $callables,
            static fn (mixed $carry, callable $callable): mixed => $callable($carry),
            null === $this->beforeInterceptors ? $payload : ($this->beforeInterceptors)($payload)
        );
    }
}
