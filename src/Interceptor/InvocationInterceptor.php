<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor;

use Panelop\Core\Interceptor\Interfaces\InvocationInterceptorInterface;
use Panelop\Core\Pipeline\Interfaces\PipelineInterface;

final readonly class InvocationInterceptor implements InvocationInterceptorInterface
{
    public function __construct(
        private PipelineInterface $pipeline
    ) {
    }

    public function __invoke(mixed ...$arguments): mixed
    {
        return ($this->pipeline)($arguments);
    }
}
