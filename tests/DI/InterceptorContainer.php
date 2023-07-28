<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\DI;

use DI\Container;
use Panelop\Core\Interceptor\DI\InterceptorFactory;

final class InterceptorContainer extends Container
{
    public function get(string $id): mixed
    {
        return (new InterceptorFactory())->create($this, parent::get($id));
    }
}
