<?php

declare(strict_types=1);

namespace Panelop\Core\Pipeline\Interfaces;

interface PipelineInterface
{
    public function pipe(callable $callable): PipelineInterface;

    public function proceed(mixed $payload = null): mixed;

    public function __invoke(mixed $payload = null): mixed;
}
