<?php

declare(strict_types=1);

namespace Panelop\Core\Tests\DI;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Panelop\Core\Interceptor\DI\InterceptorFactory;

final class InterceptorContainer extends Container
{
    /**
     * Returns an entry of the container by its name.
     *
     * @template T
     *
     * @param string|class-string<T> $id Entry name or a class name.
     *
     * @return mixed|T
     * @throws DependencyException Error while resolving the entry.
     * @throws NotFoundException No entry found for the given name.
     */
    public function get(string $id): mixed
    {
        return (new InterceptorFactory())->create($this, parent::get($id));
    }
}
