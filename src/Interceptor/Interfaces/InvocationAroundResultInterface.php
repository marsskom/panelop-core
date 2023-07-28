<?php

declare(strict_types=1);

namespace Panelop\Core\Interceptor\Interfaces;

interface InvocationAroundResultInterface
{
    public function getInvocationMethod(): InvocationMethodInterface;

    public function getPayload(): mixed;
}
