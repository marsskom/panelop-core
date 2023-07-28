<?php

declare(strict_types=1);

interface InterceptorAfterInterface
{
    public function __invoke(mixed $payload = null): mixed;
}
