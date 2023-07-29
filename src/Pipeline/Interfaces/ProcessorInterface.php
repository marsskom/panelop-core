<?php

declare(strict_types=1);

namespace Panelop\Core\Pipeline\Interfaces;

use Panelop\Core\InvocationInterface;

interface ProcessorInterface extends InvocationInterface
{
    public function __invoke(mixed $payload = null, callable ...$callables): mixed;
}
