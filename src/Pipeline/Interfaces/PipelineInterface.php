<?php

declare(strict_types=1);

namespace Panelop\Core\Pipeline\Interfaces;

use Panelop\Core\InvocationInterface;

interface PipelineInterface extends InvocationInterface
{
    public function pipe(callable $callable): PipelineInterface;

    public function __invoke(mixed $payload = null): mixed;
}
