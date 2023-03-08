<?php

declare(strict_types=1);

namespace Panelop\Core\Pipeline\Interfaces;

interface ProcessorInterface
{
    public function proceed(mixed $payload = null, callable ...$callables): mixed;

    public function __invoke(mixed $payload = null, callable ...$callables): mixed;
}
