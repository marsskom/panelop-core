<?php

declare(strict_types=1);

namespace Panelop\Core;

interface InvocationInterface
{
    public function __invoke(): mixed;
}
