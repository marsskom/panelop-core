<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\DI;

use function get_class;
use function is_object;

final readonly class Interceptor
{
    public function __construct(
        private string $entryName,
        private array  $calls
    ) {
    }

    public function __call(string $name, array $arguments): mixed
    {
        $result = $this->calls[$name](...$arguments);

        return is_object($result) && $this->entryName === get_class($result) ? $this : $result;
    }
}
