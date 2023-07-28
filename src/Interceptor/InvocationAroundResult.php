<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor;

use Panelop\Core\Interceptor\Interfaces\InvocationAroundResultInterface;
use Panelop\Core\Interceptor\Interfaces\InvocationMethodInterface;

final readonly class InvocationAroundResult implements InvocationAroundResultInterface
{
    public function __construct(
        private InvocationMethodInterface $invocationMethod,
        private mixed                     $payload = null,
    ) {
    }

    public function getInvocationMethod(): InvocationMethodInterface
    {
        return $this->invocationMethod;
    }

    public function getPayload(): mixed
    {
        return $this->payload;
    }
}
